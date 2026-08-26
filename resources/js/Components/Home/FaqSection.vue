<script setup>
import { ref } from 'vue'
import {
  Sparkles,
  ChevronDown,
} from 'lucide-vue-next'

const openIndex = ref(null)

const faqs = [
  {
    question: 'How does AI recommendation work?',
    answer:
      'Our system analyzes what you are looking for and matches your request with relevant products based on your needs, preferences and budget.',
  },
  {
    question: 'Can I compare products?',
    answer:
      'Yes. You can select products and use our AI-powered comparison feature to understand their differences and choose the better option.',
  },
  {
    question: 'What payment methods are available?',
    answer:
      'We support available secure payment methods during checkout, including mobile payment options where supported.',
  },
  {
    question: 'How long does delivery take?',
    answer:
      'Delivery time depends on your location and the product ordered. You will see the available delivery information during checkout.',
  },
]

const toggle = (index) => {
  openIndex.value =
    openIndex.value === index
      ? null
      : index
}
</script>

<template>
  <section class="bg-gray-50 py-12 sm:py-14">

    


    <div class="mx-auto max-w-4xl px-4 sm:px-6">

      <!-- HEADER -->
      <div class="mb-8 text-center">
        <h2
          class="
            text-2xl
            font-bold
            text-gray-900
            sm:text-3xl
          "
        >
          Frequently Asked Questions
        </h2>

        <p class="mt-2 text-sm text-gray-500">
          Everything you need to know about shopping on SmartCart
        </p>
      </div>


      <!-- FAQ LIST -->
      <div class="space-y-3">

        <div
          v-for="(faq, index) in faqs"
          :key="faq.question"
          class="
            overflow-hidden
            rounded-2xl
            border
            border-gray-200
            bg-white
            transition
            hover:border-blue-200
          "
        >
          <!-- QUESTION -->
          <button
            type="button"
            class="
              flex
              w-full
              items-center
              justify-between
              gap-4
              px-5
              py-4
              text-left
            "
            @click="toggle(index)"
          >
            <span
              class="
                text-sm
                font-semibold
                text-gray-900
                sm:text-base
              "
            >
              {{ faq.question }}
            </span>

            <ChevronDown
              class="
                h-5
                w-5
                shrink-0
                text-gray-400
                transition-transform
                duration-200
              "
              :class="{
                'rotate-180 text-blue-600':
                  openIndex === index
              }"
            />
          </button>


          <!-- ANSWER -->
          <Transition name="faq">
            <div
              v-if="openIndex === index"
              class="
                border-t
                border-gray-100
                px-5
                py-4
                text-sm
                leading-6
                text-gray-600
              "
            >
              {{ faq.answer }}
            </div>
          </Transition>

        </div>

      </div>

    </div>

  </section>
</template>

<style scoped>
.faq-enter-active,
.faq-leave-active {
  transition: all 0.2s ease;
}

.faq-enter-from,
.faq-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}
</style>