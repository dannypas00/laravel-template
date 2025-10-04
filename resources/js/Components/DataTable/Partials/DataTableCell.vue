<template>
  <td
    :class="[
      'whitespace-nowrap py-4 pr-3 text-sm font-medium',
      selected ? 'text-brand-dark' : 'text-gray-900',
    ]"
  >
    <slot>
      {{ header.renderBody ? header.renderBody(entry) : entry[header.key] }}
    </slot>
  </td>
</template>

<script setup lang="ts" generic="T extends Record<string, any>">
import { PropType } from 'vue';
import { TableHeader } from '../DataTableTypes';

defineProps({
  header: {
    type: Object as PropType<TableHeader<T>>,
    required: true,
  },
  entry: {
    type: Object as PropType<T>,
    required: true,
  },
  selected: {
    type: Boolean,
    required: false,
    default: false,
  },
});
</script>
