<template>
    <AdminLayout>

        <h1 class="text-2xl font-bold mb-6">  {{ isEdit ? 'Edit Product' : 'Create Product' }}</h1>

        <div class="bg-white p-6 rounded-xl shadow">
              <!-- FORM -->
        <form @submit.prevent="submit">

            <!-- ========================= -->
            <!-- BASIC INFORMATION -->
            <!-- ========================= -->
            <div class="bg-white p-6 rounded-xl shadow mb-6">

                <h2 class="text-lg font-semibold mb-4 text-gray-700">
                    Basic Information
                </h2>

                <!-- Name & Slug -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                    <!-- Name -->
                    <div>
                        <label class="block text-sm mb-1">Product Name</label>
                        <input
                            v-model="form.name"
                            type="text"
                            class="w-full border p-2 rounded"
                            placeholder="Enter product name"
                        />

                        <div v-if="form.errors.name" class="text-red-500 text-sm">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <!-- Slug -->
                    <div>
                        <label class="block text-sm mb-1">Slug</label>
                        <input
                            v-model="form.slug"
                            type="text"
                            class="w-full border p-2 rounded"
                            placeholder="auto-generated"
                        />

                        <div v-if="form.errors.slug" class="text-red-500 text-sm">
                            {{ form.errors.slug }}
                        </div>
                    </div>

                </div>

                <!-- Description -->
               <div>
                <label class="block text-sm mb-1">Description</label>

                <textarea id="editor"></textarea>

                <div v-if="form.errors.description" class="text-red-500 text-sm">
                    {{ form.errors.description }}
                </div>
                </div>

            </div>


<!-- ========================= -->
<!-- PRICING & STOCK -->
<!-- ========================= -->
<div class="bg-white p-6 rounded-xl shadow mb-6">

    <h2 class="text-lg font-semibold mb-4 text-gray-700">
        Pricing & Stock
    </h2>

    <!-- Price & Sale Price -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <!-- Price -->
        <div>
            <label class="block text-sm mb-1">Price (Ksh)</label>
            <input
                v-model="form.price"
                type="number"
                class="w-full border p-2 rounded"
                placeholder="Enter price"
            />

            <div v-if="form.errors.price" class="text-red-500 text-sm">
                {{ form.errors.price }}
            </div>
        </div>

        <!-- Sale Price -->
        <div>
            <label class="block text-sm mb-1">Sale Price (Ksh)</label>
            <input
                v-model="form.sale_price"
                type="number"
                class="w-full border p-2 rounded"
                placeholder="Optional discount price"
            />

            <div v-if="form.errors.sale_price" class="text-red-500 text-sm">
                {{ form.errors.sale_price }}
            </div>
        </div>

    </div>

    <!-- Stock -->
    <div class="mb-4">
        <label class="block text-sm mb-1">Stock Quantity</label>
        <input
            v-model="form.stock_quantity"
            type="number"
            class="w-full border p-2 rounded"
            placeholder="Enter stock quantity"
        />

        <div v-if="form.errors.stock_quantity" class="text-red-500 text-sm">
            {{ form.errors.stock_quantity }}
        </div>
    </div>

    <!-- Status Toggles -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">

    <!-- Active -->
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-700">Active</span>

        <button
            type="button"
            @click="form.is_active = !form.is_active"
            :class="form.is_active ? 'bg-green-500' : 'bg-gray-300'"
            class="w-12 h-6 flex items-center rounded-full p-1 transition"
        >
            <div
                :class="form.is_active ? 'translate-x-6' : 'translate-x-0'"
                class="w-4 h-4 bg-white rounded-full shadow transform transition"
            ></div>
        </button>
    </div>

    <!-- Featured -->
    <div class="flex items-center justify-between">
        <span class="text-sm text-gray-700">Featured Product</span>

        <button
            type="button"
            @click="form.is_featured = !form.is_featured"
            :class="form.is_featured ? 'bg-blue-500' : 'bg-gray-300'"
            class="w-12 h-6 flex items-center rounded-full p-1 transition"
        >
            <div
                :class="form.is_featured ? 'translate-x-6' : 'translate-x-0'"
                class="w-4 h-4 bg-white rounded-full shadow transform transition"
            ></div>
        </button>
    </div>

</div>


</div>




<!-- ========================= -->
<!-- CATEGORY & BRAND -->
<!-- ========================= -->
<div class="bg-white p-6 rounded-xl shadow mb-6">

    <h2 class="text-lg font-semibold mb-4 text-gray-700">
        Organization
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <!-- Category -->
        <div>
            <label class="block text-sm mb-1">Category</label>

            <select 
                v-model="form.category_id"
                class="w-full border p-2 rounded"
            >
                <option value="">Select Category</option>

                <option 
                    v-for="cat in categories" 
                    :key="cat.id" 
                    :value="cat.id"
                >
                    {{ cat.name }}
                </option>
            </select>

            <div v-if="form.errors.category_id" class="text-red-500 text-sm">
                {{ form.errors.category_id }}
            </div>
        </div>

        <!-- Brand -->
        <div>
            <label class="block text-sm mb-1">Brand</label>

            <select 
                v-model="form.brand_id"
                class="w-full border p-2 rounded"
            >
                <option value="">Select Brand</option>

                <option 
                    v-for="brand in brands" 
                    :key="brand.id" 
                    :value="brand.id"
                >
                    {{ brand.name }}
                </option>
            </select>

            <div v-if="form.errors.brand_id" class="text-red-500 text-sm">
                {{ form.errors.brand_id }}
            </div>
        </div>

    </div>

</div>




<!-- ========================= -->
<!-- PRODUCT IMAGES -->
<!-- ========================= -->
<div class="bg-white p-6 rounded-xl shadow mb-6">

    <h2 class="text-lg font-semibold mb-4 text-gray-700">
        Product Images
    </h2>

    <!-- Upload Box -->
    <div 
        class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 transition"
        @click="triggerFileInput"
    >
        <p class="text-gray-500">
            Drag & drop images here or <span class="text-blue-600 font-medium">click to upload</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">
            PNG, JPG up to 2MB
        </p>

        <!-- Hidden Input -->
        <input 
            type="file" 
            multiple 
            ref="fileInput"
            @change="handleImages"
            class="hidden"
        />
    </div>

    <!-- Preview Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">

        <div 
            v-for="(img, index) in previewImages" 
            :key="index"
            class="relative group"
        >
            <img 
                :src="img" 
                class="w-full h-32 object-cover rounded-lg"
            />

            <!-- Overlay Delete -->
            <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition rounded-lg">
                <button 
                    type="button"
                    @click="removeImage(index)"
                    class="bg-transparent text-white px-3 py-1 rounded text-sm"
                >
                    <Trash2 size="27" color="red" />
                     
                </button>
            </div>
        </div>

    </div>

</div>

<!-- ========================= -->
<!-- PRODUCT ATTRIBUTES -->
<!-- ========================= -->
<div class="bg-white p-6 rounded-xl shadow mb-6">

    <h2 class="text-lg font-semibold text-gray-700 mb-4">
        Product Attributes
    </h2>

    <!-- Attribute Rows -->
    <div v-for="(attr, index) in form.attributes" :key="index" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

        <!-- Attribute Name -->
        <div>
            <label class="block text-sm mb-1">Attribute</label>
            <input 
                v-model="attr.name"
                type="text"
                placeholder="e.g. Color"
                class="w-full border p-2 rounded"
            />
        </div>

        <!-- Value -->
        <div class="flex gap-2">
            <div class="flex-1">
                <label class="block text-sm mb-1">Value</label>
                <input 
                    v-model="attr.value"
                    type="text"
                    placeholder="e.g. Red"
                    class="w-full border p-2 rounded"
                />
            </div>

            <!-- Remove -->
            <button 
                v-if="form.attributes.length > 1"
                type="button"
                @click="removeAttribute(index)"
                class="text-red-500 mt-6"
            >
                ✕
            </button>
        </div>

    </div>

    <!-- Add Attribute Button -->
    <button 
        type="button"
        @click="addAttribute"
        class="w-full border-2 border-dashed border-gray-300 p-3 rounded-lg text-gray-600 hover:border-blue-400 hover:text-blue-600 transition"
    >
        + Add Another Attribute
    </button>

</div>



            <!-- Submit (temporary) -->
            <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-2 rounded"
            >
                {{ isEdit ? 'Update Product' : 'Save Product' }}
            </button>

        </form>
        </div>

    </AdminLayout>
</template>




<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm } from '@inertiajs/vue3'
import { watch } from 'vue'
import EasyMDE from 'easymde'
import 'easymde/dist/easymde.min.css'
import { onMounted, ref } from 'vue'
import { Trash2 } from 'lucide-vue-next'
import { route } from 'ziggy-js'

const props = defineProps({
    categories: Array,
    brands: Array,
    product: Object
})

const isEdit = props.product ? true : false



let initialAttributes = [
    { name: '', value: '' }
]

if (props.product && props.product.attributes && props.product.attributes.length > 0) {
    initialAttributes = props.product.attributes.map(attr => ({
        name: attr.attribute_name,
        value: attr.attribute_value
    }))
}




const form = useForm({
    name: props.product?.name || '',
    slug: props.product?.slug || '',
    description: props.product?.description || '',

    price: props.product?.price || '',
    sale_price: props.product?.sale_price || '',
    stock_quantity: props.product?.stock_quantity || '',

    category_id: props.product?.category_id || '',
    brand_id: props.product?.brand_id || '',

    is_active: props.product?.is_active ?? true,
    is_featured: props.product?.is_featured ?? false,

    images: [],

    attributes: initialAttributes,
})



// Auto-generate slug
watch(() => form.name, (value) => {
    form.slug = value
        .toLowerCase()
        .replace(/\s+/g, '-')
        .replace(/[^\w-]+/g, '')
})

// Submit (temporary)
const submit = () => {
    if (isEdit) {
       form.transform((data) => ({
    ...data,
    _method: 'put'
})).post(route('admin.products.update', props.product.id), {
    forceFormData: true
})
    } else {
        form.post(route('admin.products.store'), {
            forceFormData: true,
            onSuccess: () => {
                form.reset()
                form.description = ''
                previewImages.value = []
            }
        })
    }
}



const editor = ref(null)

onMounted(() => {
    editor.value = new EasyMDE({
        element: document.getElementById('editor'),
        placeholder: 'Write product description...',
    })

 editor.value.value(form.description || '')
    // Sync with form
    editor.value.codemirror.on('change', () => {
        form.description = editor.value.value()
    })
})



const previewImages = ref([])

// Handle image upload
function handleImages(e) {
    const files = Array.from(e.target.files)

    files.forEach(file => {
        form.images.push(file)

        // preview
        previewImages.value.push(URL.createObjectURL(file))
    })
}

// Remove image
function removeImage(index) {
    form.images.splice(index, 1)
    previewImages.value.splice(index, 1)
}



const fileInput = ref(null)

const triggerFileInput = () => {
    fileInput.value.click()
}





const addAttribute = () => {
    form.attributes.push({
        name: '',
        value: ''
    })
}

const removeAttribute = (index) => {
    form.attributes.splice(index, 1)
}
</script>