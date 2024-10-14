<template>
  <th
    scope="col"
    class="min-w-max text-left text-sm font-semibold text-gray-900"
    :class="{ 'd-none': hideTitle }"
  >
    <div class="flex w-full flex-row items-center gap-2">
      <FontAwesomeIcon
        v-if="header.sortable"
        :icon="{
          prefix: 'fas',
          iconName: 'arrow-up',
        }"
        :rotation="sortDirection === SortDirection.Desc ? 180 : undefined"
        class="cursor-pointer"
        :class="{ 'text-brand-light': sortDirection !== SortDirection.None }"
        @click="() => (sortDirection = nextSortDirection(sortDirection))"
      />
      <div class="group relative h-full grow py-3.5 pr-3">
        <span
          :class="{
            'select-none group-hover:text-transparent group-has-[.has-input]:text-transparent group-has-[:focus]:text-transparent':
              header.filter,
          }"
        >
          {{ header.title }}
        </span>
        <div
          v-if="header.filter && filterComponent"
          class="absolute inset-0 hidden h-full group-hover:inline has-[.has-input]:inline has-[:focus]:inline"
        >
          <Component :is="filterComponent" :filter="header.filter" />
        </div>
      </div>
    </div>
  </th>
</template>

<script setup lang="ts" generic="T extends Record<string, any>">
import { computed, ComputedRef, inject, PropType, Ref } from 'vue';
import { DateFilterType, FilterType, TableHeader } from '../DataTableTypes';
import DataTableSearchFilter from './Filters/DataTableSearchFilter.vue';
import DataTableDateRangeFilter from './Filters/DataTableDateRangeFilter.vue';
import DataTableDateExactFilter from './Filters/DataTableDateExactFilter.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import {
  nextSortDirection,
  SortDirection,
} from '../../../Utilities/SortDirection';

const props = defineProps({
  header: {
    type: Object as PropType<TableHeader<T>>,
    required: true,
  },

  hideTitle: {
    type: Boolean,
    required: false,
    default: false,
  },
});

let sortDirectionMap: undefined | Ref<Record<string, SortDirection>>;
if (props.header.sortable) {
  sortDirectionMap = inject('sort-values');
}

const sortDirection: ComputedRef<SortDirection> = computed(() => {
  if (!sortDirectionMap?.value) {
    return SortDirection.None;
  }
  return sortDirectionMap.value[props.header.key];
});

const filterComponent = computed(() => {
  if (!props.header.filter) {
    return undefined;
  }
  switch (props.header.filter.type) {
    case FilterType.Search:
      return DataTableSearchFilter;
    case FilterType.Date:
      switch (props.header.filter.dateFilterType) {
        // These use the prop type to disable one or the other component
        case DateFilterType.FromDate:
        case DateFilterType.UntilDate:
        case DateFilterType.DateRange:
          return DataTableDateRangeFilter;
        case DateFilterType.ExactDate:
          return DataTableDateExactFilter;
        default:
          return undefined;
      }
    default:
      return undefined;
  }
});
</script>
