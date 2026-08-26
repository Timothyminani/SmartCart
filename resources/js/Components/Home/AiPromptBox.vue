<script setup>
import { computed, nextTick, ref } from 'vue'
import { ArrowRight, Loader2 } from 'lucide-vue-next'

const props = defineProps({
  modelValue: {
    type: String,
    default: '',
  },

  loading: {
    type: Boolean,
    default: false,
  },

  placeholder: {
    type: String,
    default: "Ask anything... e.g. 'affordable laptop for programming'",
  },
})

const emit = defineEmits([
  'update:modelValue',
  'submit',
])

const textareaRef = ref(null)

const prompt = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})

const canSubmit = computed(() => {
  return prompt.value.trim().length > 0 && !props.loading
})

const resizeTextarea = async () => {
  await nextTick()

  const textarea = textareaRef.value

  if (!textarea) return

  textarea.style.height = 'auto'

  textarea.style.height = `${Math.min(
    textarea.scrollHeight,
    120
  )}px`
}

const handleInput = () => {
  resizeTextarea()
}

const submit = () => {
  const value = prompt.value.trim()

  if (!value || props.loading) return

  emit('submit', value)
}

const handleKeydown = (event) => {
  if (event.key !== 'Enter') return

  // Shift + Enter = new line
  if (event.shiftKey) return

  event.preventDefault()

  submit()
}

const focus = () => {
  textareaRef.value?.focus()
}

defineExpose({
  focus,
})
</script>

<template>
  <div class="w-full">
    <div
      class="
        relative
        rounded-3xl
        border border-gray-200
        bg-white
        p-4
        shadow-md
        transition
        focus-within:border-blue-300
        focus-within:shadow-lg
      "
    >
      <textarea
        ref="textareaRef"
        v-model="prompt"
        rows="1"
        :placeholder="placeholder"
        :disabled="loading"
        class="
          block
          min-h-[44px]
          max-h-[120px]
          w-full
          resize-none
          overflow-y-auto
          bg-transparent
          pr-14
          text-sm
          leading-6
          text-gray-900
          placeholder:text-gray-400
          outline-none
          border-none
          focus:outline-none
          focus:ring-0
          disabled:cursor-not-allowed
          disabled:opacity-70
        "
        @input="handleInput"
        @keydown="handleKeydown"
      />

      <button
        type="button"
        :disabled="!canSubmit"
        aria-label="Search with AI"
        class="
          absolute
          bottom-3
          right-3
          flex
          h-10
          w-10
          items-center
          justify-center
          rounded-full
          bg-gray-900
          text-white
          transition
          hover:bg-black
          disabled:cursor-not-allowed
          disabled:bg-gray-300
        "
        @click="submit"
      >
        <Loader2
          v-if="loading"
          class="h-4 w-4 animate-spin"
        />

        <ArrowRight
          v-else
          class="h-4 w-4"
        />
      </button>
    </div>

    <p class="mt-2 text-xs text-gray-400">
      Press Enter to search · Shift + Enter for a new line
    </p>
  </div>
</template>