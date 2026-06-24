
<template>
    <GuestLayout>
        <Head title="Log in" />

<template #hero>

        <h1 class="text-5xl font-bold leading-tight">
             Shop Smarter With AI
        </h1>

        <p class="mt-6 text-lg text-blue-100 leading-relaxed">
        Compare products, discover the best deals and
        make confident buying decisions.
        </p>

 </template>



        

        <div class="w-full max-w-md mx-auto ">

            <!-- Card -->
            <div class="bg-white shadow-xl rounded-3xl p-8 border border-gray-100">

                <!-- Header -->
                <div class="text-center mb-4">

                    <h1 class="text-3xl font-bold text-gray-900">
                        Welcome Back
                    </h1>

                    <p class="text-gray-500 mt-2">
                        Sign in to continue shopping
                    </p>

                </div>

                <!-- Success Message -->
                <div
                    v-if="status"
                    class="mb-6 rounded-xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-700"
                >
                    {{ status }}
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-4">

                    <!-- Email -->
                    <div>

                        <InputLabel
                            for="email"
                            value="Email Address"
                        />

                        <TextInput
                            id="email"
                            type="email"
                            class="mt-2 block w-full rounded-xl"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.email"
                        />

                    </div>

                    <!-- Password -->
                    
            <div>

                <InputLabel
                    for="password"
                    value="Password"
                />

                <div class="relative mt-2">

                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full rounded-xl pr-12"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />

                    <!-- Toggle Button -->
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600"
                    >
                        <EyeOff
                            v-if="!showPassword"
                            class="w-5 h-5"
                        />

                        <Eye
                            v-else
                            class="w-5 h-5"
                        />
                    </button>

                </div>

    <InputError
        class="mt-2"
        :message="form.errors.password"
    />

</div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between">

                        <label class="flex items-center">

                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />

                            <span class="ml-2 text-sm text-gray-600">
                                Remember me
                            </span>

                        </label>

                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-blue-600 hover:text-blue-700"
                        >
                            Forgot Password?
                        </Link>

                    </div>

                    <!-- Login Button -->
                    <PrimaryButton
                        class="w-full justify-center py-3 rounded-xl"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                    >
                        Log In
                    </PrimaryButton>

                </form>

                <!-- Divider -->
                <div class="relative my-5">

                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-200"></div>
                    </div>

                    <div class="relative flex justify-center">
                        <span class="bg-white px-4 text-sm text-gray-500">
                            OR
                        </span>
                    </div>

                </div>

                <!-- Google Login -->
                <a
                    href="/auth/google"
                    class="w-full flex items-center justify-center gap-3 border border-gray-300 rounded-xl py-3 hover:bg-gray-50 transition"
                >

                    <img
                        src="https://www.svgrepo.com/show/475656/google-color.svg"
                        class="w-5 h-5"
                    >

                    <span class="font-medium text-gray-700">
                        Continue with Google
                    </span>

                </a>

                <!-- Register -->
                <div class="mt-8 text-center text-sm text-gray-600">

                    Don't have an account?

                    <Link
                        :href="route('register')"
                        class="text-blue-600 hover:text-blue-700 font-medium"
                    >
                        Create Account
                    </Link>

                </div>

            </div>

        </div>

    </GuestLayout>
</template>


<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Eye, EyeOff } from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};


const showPassword = ref(false);

</script>

