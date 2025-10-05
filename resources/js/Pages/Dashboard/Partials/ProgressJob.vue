<template>
  <div data-testid="progress-job" class="flex gap-6 py-2">
    <Icon
      :icon="statusInfo.icon"
      class="my-auto size-8"
      :class="statusInfo.class"
    />
    <span class="my-auto w-1/3 grow whitespace-nowrap">{{ identifier }}</span>
    <Progress class="my-auto" :model-value="(progress / max) * 100" />
    <span v-if="max > 1">{{ progress }}/{{ max }}</span>
  </div>
</template>

<script setup lang="ts">
import Progress from '@/Components/ui/progress/Progress.vue';
import { Icon } from '@iconify/vue';
import { useEcho } from '@laravel/echo-vue';
import { computed, ref } from 'vue';

export type ProgressJobProps = {
  identifier: string;
  duration: number;
};

const props = defineProps<ProgressJobProps>();

const progress = ref(0);
const max = ref(Math.max(props.duration, 1));
const status = ref('queued');

useEcho(
  `jobs.${props.identifier}`,
  '.App\\Events\\JobUpdatedEvent',
  (event: {
    identifier: string;
    status: string;
    progress: number;
    maxProgress: number;
  }) => {
    progress.value = event.progress;
    max.value = event.maxProgress;
    status.value = event.status;
  },
);

const statusInfo = computed(() => {
  switch (status.value) {
    case 'queued':
      return { icon: 'mdi:progress-upload', class: 'text-amber-400' };
    case 'running':
      return { icon: 'mdi:progress-clock', class: 'text-foreground' };
    case 'succeeded':
      return { icon: 'mdi:progress-check', class: 'text-green-300' };
    case 'failed':
      return { icon: 'mdi:progress-close', class: 'text-red-500' };
    default:
      return { icon: 'mdi:progress-question', class: 'text-foreground/50' };
  }
});
</script>
