<template>
    <footer
        class="mt-10 border-t bg-blue-900"
    >
        <div
            class="
                mx-auto
                grid
                max-w-7xl
                grid-cols-1
                gap-8
                px-4
                py-10
                text-sm
                text-white
                sm:grid-cols-2
                sm:px-6
                lg:grid-cols-4
                lg:px-8
            "
        >

            <!-- ========================================================= -->
            <!-- SMARTCART -->
            <!-- ========================================================= -->

            <div>
                <h3
                    class="
                        mb-3
                        text-lg
                        font-bold
                        text-white
                    "
                >
                    SmartCart
                </h3>

                <p
                    class="
                        max-w-xs
                        leading-6
                        text-blue-100
                    "
                >
                    A demo e-commerce platform with AI-powered product
                    search and intelligent product comparison.
                </p>
            </div>


            <!-- ========================================================= -->
            <!-- SHOP -->
            <!-- ========================================================= -->

            <div>
                <h3
                    class="
                        mb-3
                        font-semibold
                        text-white
                    "
                >
                    Shop
                </h3>

                <ul
                    class="
                        space-y-2
                        text-blue-100
                    "
                >
                    <li>
                        <a
                            href="/productListing"
                            class="
                                transition
                                hover:text-white
                            "
                        >
                            All Products
                        </a>
                    </li>

                    <li>
                        <a
                            href="/productListing?sort=newest"
                            class="
                                transition
                                hover:text-white
                            "
                        >
                            New Arrivals
                        </a>
                    </li>
                </ul>
            </div>


            <!-- ========================================================= -->
            <!-- FEATURES -->
            <!-- ========================================================= -->

            <div>
                <h3
                    class="
                        mb-3
                        font-semibold
                        text-white
                    "
                >
                    Features
                </h3>

                <ul
                    class="
                        space-y-2
                        text-blue-100
                    "
                >
                    <li>
                        <span>
                            AI Smart Search
                        </span>
                    </li>

                    <li>
                        <span>
                            AI Product Comparison
                        </span>
                    </li>

                    <li>
                        <span>
                            Product Filters
                        </span>
                    </li>

                    <li>
                        <span>
                            Wishlist & Cart
                        </span>
                    </li>
                </ul>
            </div>


            <!-- ========================================================= -->
            <!-- CONNECT -->
            <!-- ========================================================= -->

            <div>
                <h3
                    class="
                        mb-3
                        font-semibold
                        text-white
                    "
                >
                    Connect
                </h3>

                <ul
                    class="
                        space-y-2
                        text-blue-100
                    "
                >
                    <li>
                        <a
                            href="https://devtim.site"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="
                                transition
                                hover:text-white
                            "
                        >
                            My Portfolio
                        </a>
                    </li>

                    <li>
                        <a
                            href="https://github.com/Timothyminani"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="
                                transition
                                hover:text-white
                            "
                        >
                            GitHub
                        </a>
                    </li>
                </ul>
            </div>

        </div>


        <!-- ========================================================= -->
        <!-- BOTTOM -->
        <!-- ========================================================= -->

        <div
            class="
                border-t
                border-blue-800
            "
        >
            <div
                class="
                    mx-auto
                    flex
                    max-w-7xl
                    flex-col
                    items-center
                    justify-between
                    gap-2
                    px-4
                    py-4
                    text-center
                    text-xs
                    text-blue-100
                    sm:flex-row
                    sm:px-6
                    lg:px-8
                "
            >
                <p>
                    © 2026 SmartCart. A demo e-commerce project built by
                    <span class="font-semibold text-white">
                        Timothy Minani
                    </span>.
                </p>

                <a
                    href="https://devtim.site"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="
                        font-semibold
                        text-white
                        transition
                        hover:text-blue-200
                        hover:underline
                    "
                >
                    View Portfolio →
                </a>
            </div>
        </div>
    </footer>
</template>