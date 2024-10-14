<template>
    <button
      type="button"
      class="inline-flex items-center gap-x-1.5 rounded-md px-3 py-2 text-sm font-semibold shadow-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2"
    >
      <FontAwesomeIcon
        v-bind="icon"
        class="-ml-0.5 h-5 w-5"
        aria-hidden="true"
      />
      {{ text }}
    </button>
</template>

<script setup lang="ts">
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { FontAwesomeIconProps } from '../Icons/FontAwesomeIconProps';
import { PropType } from 'vue';

// Add text-white bg-indigo-600 hover:bg-indigo-500 focus-visible:outline-indigo-600 (or equivalent) to button to change colour

defineProps({
  icon: {
    type: Object as PropType<FontAwesomeIconProps>,
    required: true,
  },
  text: {
    type: String,
    required: true,
  },
  href: {
    type: String,
    required: false,
    default: undefined,
  },
});
</script>
