<template>
  <div v-if="title || description" class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
      <h1 class="text-base font-semibold leading-6 text-gray-900">
        {{ title }}
      </h1>
      <p class="mt-2 text-sm text-gray-700">
        {{ description }}
      </p>
    </div>
  </div>

  <div
    class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8"
    :class="{ 'pt-8': bulkActions.length > 0 }"
  >
    <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
      <div class="relative">
        <div
          v-if="selected.length > 0"
          class="absolute -top-9 left-3 flex h-12 items-center space-x-3"
        >
          <TableBulkAction
            v-for="action in bulkActions"
            :key="action.title"
            v-model:selected="selected"
            :action="action"
          />
        </div>
        <table class="min-w-full table-fixed divide-y divide-gray-300">
          <thead>
            <TableHead
              v-bind="{
                headers,
                identifier,
                data,
                selectable,
              }"
              v-model:selected="selected"
            />
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr
              v-for="entry in data"
              :key="entry[identifier]"
              :class="[selected.includes(entry[identifier]) && 'bg-gray-50']"
            >
              <td class="relative px-7 sm:w-12 sm:px-6" v-if="selectable">
                <div
                  v-if="selected.includes(entry[identifier])"
                  class="absolute inset-y-0 left-0 w-0.5 bg-indigo-600"
                />
                <input
                  v-model="selected"
                  type="checkbox"
                  :class="[
                    'absolute left-4 top-1/2 -mt-2 h-4 w-4',
                    'rounded border-gray-300 text-indigo-600 focus:ring-indigo-600',
                  ]"
                  :value="entry[identifier]"
                />
              </td>
              <DataTableCell
                v-for="header in headers"
                :key="header.key"
                :header="header"
                :entry="entry"
                :selected="selected?.includes(entry[identifier])"
              >
                <slot v-if="header.bodySlot" :name="header.bodySlot" :header="header" :entry="entry" />
              </DataTableCell>
              <EditButton />
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts" generic="T extends Record<string, any>">
import { computed, ModelRef, PropType } from 'vue';
import { BulkOption, TableHeader } from './DataTableTypes';
import DataTableCell from './Partials/DataTableCell.vue';
import EditButton from './Partials/EditButton.vue';
import TableHead from './Partials/TableHead.vue';
import TableBulkAction from './Partials/TableBulkAction.vue';

const emit = defineEmits(['update:selected']);

const selectedModel: ModelRef<Array<T>> = defineModel('selected', {
  type: Array<T>,
  required: false,
  default: [],
});

const selected = computed({
  get: () => selectedModel.value,
  set: value => emit('update:selected', value),
});

defineProps({
  headers: {
    type: Array as PropType<TableHeader<T>[]>,
    required: true,
  },

  data: {
    type: Array as PropType<T[]>,
    required: true,
  },

  identifier: {
    type: String,
    required: false,
    default: 'id',
  },

  selectable: {
    type: Boolean,
    required: false,
    default: false,
  },

  rowClickable: {
    type: Boolean,
    required: false,
    default: false,
  },

  bulkActions: {
    type: Array as PropType<BulkOption<T>[]>,
    required: false,
    default: () => [],
  },

  title: {
    type: String,
    required: false,
    default: undefined,
  },

  description: {
    type: String,
    required: false,
    default: undefined,
  },
});
</script>
