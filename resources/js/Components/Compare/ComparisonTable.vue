<script setup>
import { computed } from 'vue'


const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| MERGED ATTRIBUTES
|--------------------------------------------------------------------------
*/

const mergedAttributes = computed(() => {

    const attributes = []

    props.products.forEach(product => {

        product.attributes?.forEach(attribute => {

            if (
                !attributes.includes(
                    attribute.attribute_name
                )
            ) {
                attributes.push(
                    attribute.attribute_name
                )
            }

        })

    })

    return attributes
})


/*
|--------------------------------------------------------------------------
| ATTRIBUTE VALUE
|--------------------------------------------------------------------------
*/

const getValue = (
    product,
    attributeName
) => {

    const attribute =
        product.attributes?.find(
            item =>
                item.attribute_name ===
                attributeName
        )

    return (
        attribute?.attribute_value ||
        '—'
    )
}
</script>


<template>

    <section>

        <!-- ===================================================== -->
        <!-- DESKTOP TABLE -->
        <!-- ===================================================== -->

        <div
            v-if="mergedAttributes.length"
            class="
                hidden
                md:block

                overflow-x-auto

                rounded-2xl

                border
                border-gray-200

                bg-white
            "
        >

            <table
                class="
                    w-full
                    min-w-[720px]

                    border-collapse

                    bg-white
                "
            >

                <!-- HEADER -->

                <thead>

                    <tr class="bg-blue-50/70">

                        <!-- SPECIFICATION -->

                        <th
                            class="
                                sticky
                                left-0
                                z-10

                                w-52
                                min-w-[180px]

                                bg-blue-50

                                border-b
                                border-r
                                border-gray-200

                                px-4
                                py-4

                                text-left
                                text-sm
                                font-semibold
                                text-gray-900
                            "
                        >
                            Specification
                        </th>


                        <!-- PRODUCTS -->

                        <th
                            v-for="product in products"
                            :key="product.id"
                            class="
                                min-w-[220px]

                                border-b
                                border-r
                                last:border-r-0
                                border-gray-200

                                px-4
                                py-3

                                text-left
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-center
                                    gap-3
                                "
                            >

                                <img
                                    :src="
                                        product.images?.[0]
                                            ? `/storage/${product.images[0].image_path}`
                                            : '/placeholder.png'
                                    "
                                    :alt="product.name"
                                    class="
                                        w-10
                                        h-10

                                        rounded-lg

                                        object-cover

                                        border
                                        border-gray-200

                                        shrink-0
                                    "
                                />


                                <div class="min-w-0">

                                    <p
                                        class="
                                            text-sm
                                            font-semibold
                                            text-gray-900
                                            line-clamp-2
                                        "
                                    >
                                        {{ product.name }}
                                    </p>


                                    <p
                                        v-if="product.brand?.name"
                                        class="
                                            text-xs
                                            text-gray-500
                                            mt-0.5
                                        "
                                    >
                                        {{ product.brand.name }}
                                    </p>

                                </div>

                            </div>

                        </th>

                    </tr>

                </thead>


                <!-- BODY -->

                <tbody>

                    <tr
                        v-for="(
                            attribute,
                            index
                        ) in mergedAttributes"
                        :key="attribute"
                        :class="
                            index % 2 === 0
                                ? 'bg-white'
                                : 'bg-gray-50/50'
                        "
                    >

                        <!-- ATTRIBUTE -->

                        <td
                            class="
                                sticky
                                left-0
                                z-[5]

                                bg-inherit

                                border-b
                                border-r
                                border-gray-200

                                px-4
                                py-3.5

                                text-sm
                                font-medium
                                text-gray-800

                                capitalize
                            "
                        >
                            {{ attribute }}
                        </td>


                        <!-- VALUES -->

                        <td
                            v-for="product in products"
                            :key="product.id"
                            class="
                                border-b
                                border-r
                                last:border-r-0
                                border-gray-200

                                px-4
                                py-3.5

                                text-sm
                                text-gray-600

                                align-top
                            "
                        >
                            {{
                                getValue(
                                    product,
                                    attribute
                                )
                            }}
                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- ===================================================== -->
        <!-- MOBILE COMPARISON -->
        <!-- ===================================================== -->

        <div
            v-if="mergedAttributes.length"
            class="
                md:hidden
                space-y-3
            "
        >

            <div
                v-for="attribute in mergedAttributes"
                :key="attribute"
                class="
                    bg-white

                    border
                    border-gray-200

                    rounded-xl

                    overflow-hidden
                "
            >

                <!-- ATTRIBUTE NAME -->

                <div
                    class="
                        px-3
                        py-2.5

                        bg-gray-50

                        border-b
                        border-gray-100
                    "
                >

                    <p
                        class="
                            text-sm
                            font-semibold
                            text-gray-900
                            capitalize
                        "
                    >
                        {{ attribute }}
                    </p>

                </div>


                <!-- PRODUCT VALUES -->

                <div
                    class="
                        divide-y
                        divide-gray-100
                    "
                >

                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="
                            flex
                            items-center
                            justify-between

                            gap-3

                            px-3
                            py-3
                        "
                    >

                        <!-- PRODUCT -->

                        <div
                            class="
                                flex
                                items-center
                                gap-2.5

                                min-w-0
                            "
                        >

                            <img
                                :src="
                                    product.images?.[0]
                                        ? `/storage/${product.images[0].image_path}`
                                        : '/placeholder.png'
                                "
                                :alt="product.name"
                                class="
                                    w-8
                                    h-8

                                    rounded-lg

                                    object-cover

                                    border
                                    border-gray-100

                                    shrink-0
                                "
                            />


                            <div class="min-w-0">

                                <p
                                    class="
                                        text-xs
                                        font-medium
                                        text-gray-700

                                        truncate
                                        max-w-[150px]
                                    "
                                >
                                    {{ product.name }}
                                </p>


                                <p
                                    v-if="product.brand?.name"
                                    class="
                                        text-[10px]
                                        text-gray-400
                                        mt-0.5
                                    "
                                >
                                    {{ product.brand.name }}
                                </p>

                            </div>

                        </div>


                        <!-- VALUE -->

                        <p
                            class="
                                text-sm
                                font-medium
                                text-gray-900

                                text-right

                                shrink-0

                                max-w-[45%]
                            "
                        >
                            {{
                                getValue(
                                    product,
                                    attribute
                                )
                            }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <!-- ===================================================== -->
        <!-- NO SPECIFICATIONS -->
        <!-- ===================================================== -->

        <div
            v-else
            class="
                bg-white

                border
                border-gray-200

                rounded-2xl

                px-5
                py-8

                text-center
            "
        >

            <p
                class="
                    text-sm
                    text-gray-500
                "
            >
                No specifications available for these products.
            </p>

        </div>

    </section>

</template>