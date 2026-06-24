<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import {
    User,
    Mail,
    Lock,
    Eye,
    EyeOff
} from 'lucide-vue-next';

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () =>
            form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- LEFT SIDE HERO -->
        <template #hero>

            <h1 class="text-5xl font-bold leading-tight">
                Create Your Account
            </h1>

            <p class="mt-6 text-lg text-blue-100 leading-relaxed">
                Join thousands of shoppers and start discovering
                amazing products powered by AI recommendations.
            </p>

        </template>

 <div class="w-full max-w-3xl mx-auto">

    <!-- Card -->
    <div class="bg-white shadow-xl rounded-3xl py-5 px-10 border border-gray-100">

        <!-- HEADER -->
        <div class="text-center mb-4">

            <h1 class="text-3xl font-bold text-gray-900">
                Create Account
            </h1>

            <p class="text-gray-500 mt-2">
                Join us and start shopping smarter
            </p>

        </div>

        <!-- FORM -->
 <form
    @submit.prevent="submit"
    class="space-y-4"
>

    <!-- 1 Column Grid -->
    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">

        <!-- NAME -->
        <div>
            <InputLabel
                for="name"
                value="Full Name"
            />

            <div class="relative mt-2">

               <User class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 peer-focus:text-blue-800"  size="20" />  

                <TextInput
                    id="name"
                    type="text"
                    class="block w-full rounded-xl pl-10"
                    v-model="form.name"
                    placeholder="John Doe"
                    required
                    autofocus
                    autocomplete="name"
                />

            </div>

            <InputError
                class="mt-2"
                :message="form.errors.name"
            />
        </div>

        <!-- EMAIL -->
        <div>
            <InputLabel
                for="email"
                value="Email Address"
            />

            <div class="relative mt-2">

                <Mail class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" size="20" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-xl pl-10"
                    v-model="form.email"
                    placeholder="john@example.com"
                    required
                    autocomplete="username"
                />

            </div>

            <InputError
                class="mt-2"
                :message="form.errors.email"
            />
        </div>

        <!-- PASSWORD -->
     <div class="grid md:grid-cols-2 gap-4">
        <div>
            <InputLabel
                for="password"
                value="Password"
            />

            <div class="relative mt-2">

                

                <TextInput
                    id="password"
                    :type="showPassword ? 'text' : 'password'"
                    class="block w-full rounded-xl pl-10 pr-10"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    
                />

                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
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

        <!-- CONFIRM PASSWORD -->
        <div>
            <InputLabel
                for="password_confirmation"
                value="Confirm Password"
            />

        <div class="relative mt-2">

            
            <TextInput
                id="password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                class="block w-full rounded-xl pl-10 pr-10"
                v-model="form.password_confirmation"
                required
                autocomplete="new-password"
                
            />

            <button
                type="button"
                @click="showConfirmPassword = !showConfirmPassword"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
                <EyeOff
                    v-if="!showConfirmPassword"
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
                :message="form.errors.password_confirmation"
            />
        </div>

</div>

    </div>

    <!-- REGISTER BUTTON -->
    <PrimaryButton
        class="w-full justify-center py-3 rounded-xl mt-2"
        :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing"
    >
        Create Account
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

        <!-- Google Signup -->
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

        <!-- Login link -->
        <div class="mt-4 text-center text-sm text-gray-600">

            Already have an account?

            <Link
                :href="route('login')"
                class="text-blue-600 hover:text-blue-700 font-medium"
            >
                Sign In
            </Link>

        </div>

    </div>

</div>

    </GuestLayout>
</template>