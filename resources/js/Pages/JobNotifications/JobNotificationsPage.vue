<template>
  <PageHeader header="Job Notifications" />

  <IconButton
    :icon="{ icon: 'fa-plus' }"
    text="Start new job"
    class="bg-green-600 text-white hover:bg-green-500 focus-visible:outline-green-600"
    @click="startNewJob"
  />

  <DataTable :data="jobs" :headers="getTableHeaders()" identifier="identifier">
    <template #progress-body="{ entry }">
      <div class="w-24" v-if="entry.maxProgress > 0">
        <div class="flex items-center space-x-2">
          <span>{{ entry.progress }} / {{ entry.maxProgress }}</span>
          <span
            >{{
              ((entry.progress / entry.maxProgress) * 100).toFixed(0)
            }}%</span
          >
        </div>
        <div class="w-full overflow-hidden rounded-full bg-gray-200">
          <div
            class="h-2 rounded-full bg-indigo-600"
            :style="{ width: (entry.progress / entry.maxProgress) * 100 + '%' }"
          ></div>
        </div>
      </div>
      <span v-else> Waiting to start </span>
    </template>
  </DataTable>
</template>

<script setup lang="ts">
import PageHeader from '../../Components/Layout/PageHeader.vue';
import IconButton from '../../Components/Buttons/IconButton.vue';
import { ref, Ref } from 'vue';
import DataTable from '../../Components/DataTable/DataTable.vue';
import {
  DateFilterType,
  FilterType,
  TableHeader,
} from '../../Components/DataTable/DataTableTypes';
import axios from 'axios';
import { JobStatusEnum } from '../../Types/generated';

type Job = {
  identifier: string;
  progress: number;
  maxProgress: number;
  status: JobStatusEnum;
  started: Date | null;
  finished: Date | null;
};

const jobs: Ref<Record<string, Job>> = ref({});

function getTableHeaders(): TableHeader<Job>[] {
  return [
    {
      key: 'identifier',
      title: 'Identifier',
      sortable: true,
      filter: {
        filter: 'identifier',
        type: FilterType.Search,
        placeholder: 'Search by identifier',
      },
    },
    {
      key: 'status',
      title: 'Status',
      sortable: true,
      filter: {
        filter: 'status',
        type: FilterType.Select,
        options: ['queued', 'running', 'done'],
      },
    },
    {
      key: 'progress',
      title: 'Progress',
      bodySlot: 'progress-body',
    },
    {
      key: 'started',
      title: 'Started',
      sortable: true,
      renderBody(entry): string {
        return entry.started?.toLocaleTimeString() ?? '';
      },
      filter: {
        filter: 'started',
        type: FilterType.Date,
        dateFilterType: DateFilterType.TimeRange,
      },
    },
    {
      key: 'finished',
      title: 'Finished',
      sortable: true,
      renderBody(entry): string {
        return entry.finished?.toLocaleTimeString() ?? '';
      },
      filter: {
        filter: 'finished',
        type: FilterType.Date,
        dateFilterType: DateFilterType.TimeRange,
      },
    },
  ];
}

async function startNewJob() {
  const response = await axios.post(route('web.api.test-job'));

  jobs.value[response.data] = {
    identifier: response.data,
    status: JobStatusEnum.QUEUED,
    progress: 0,
    maxProgress: 0,
    started: null,
    finished: null,
  };

  window.Echo.private(`jobs.${response.data}`).listen(
    '.App\\Events\\JobUpdatedEvent',
    (event: {
      identifier: string;
      status: JobStatusEnum;
      progress: number;
      maxProgress: number;
    }) => {
      console.log('Job update', event);
      let job: Partial<Job> = jobs.value[event.identifier];

      job.status = event.status;
      job.progress = event.progress;
      job.maxProgress = event.maxProgress;

      switch (event.status) {
        case JobStatusEnum.RUNNING:
          if (!job.started) {
            job.started = new Date();
          }
          break;
        case JobStatusEnum.FAILED:
        case JobStatusEnum.SUCCEEDED:
          job.finished = new Date();
          break;
      }
    }
  );
}
</script>
