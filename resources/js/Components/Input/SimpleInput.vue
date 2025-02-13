<template>
  <div>
    <label
      :for="identifier"
      class="relative block text-sm font-medium leading-6 text-gray-900"
      v-if="label"
    >
      {{ label }}
      <span v-if="required" class="absolute top-0 text-sm text-red-500">*</span>
    </label>

    <div class="mt-1">
      <input
        :id="identifier"
        v-model="value"
        :name="identifier"
        :type
        :autocomplete
        :required
        class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6"
      />
      <span
        v-if="error ?? $page.props.errors?.[identifier]"
        class="text-sm text-red-500"
      >
        {{ error ?? $page.props.errors?.[identifier] }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, InputTypeHTMLAttribute, PropType } from 'vue';

defineProps({
  label: {
    type: String,
    required: false,
    default: undefined,
  },
  identifier: {
    type: String,
    required: true,
  },
  type: {
    type: String as PropType<InputTypeHTMLAttribute>,
    required: false,
    default: 'text',
  },
  autocomplete: {
    type: String,
    required: false,
    default: 'off',
  },
  required: {
    type: Boolean,
    required: false,
    default: false,
  },
  error: {
    type: String,
    required: false,
    default: undefined,
  }
});

const modelValue = defineModel({ required: true });

const value = computed({
  get: () => modelValue.value,
  set: (newValue: string) => {
    modelValue.value = newValue;
  },
});
</script>
