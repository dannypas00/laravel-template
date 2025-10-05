<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <h2
        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
      >
        Dashboard
      </h2>
    </template>

    <div class="py-12">
      <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
        <div
          class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg"
        >
          <div class="p-6 text-foreground">
            <div class="flex justify-between">
              <span class="text-xl">Test job progress</span>
              <form class="flex gap-4" @submit.prevent="startJob">
                <Input
                  class="ring-1 ring-foreground/50"
                  placeholder="New job duration (seconds)"
                  v-model="duration"
                />

                <Button variant="default" class="text-background" type="submit">
                  Start&nbsp;Job
                </Button>
              </form>
            </div>

            <div class="flex flex-col pt-6">
              <span class="text-lg">Active jobs:</span>

              <ProgressJob
                v-for="(duration, identifier) in jobs"
                :key="identifier"
                :identifier="identifier"
                :duration="duration"
                class="mt-4 w-full"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import Button from '@/Components/ui/button/Button.vue';
import Input from '@/Components/ui/input/Input.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import { Ref, ref } from 'vue';
import ProgressJob from './Dashboard/Partials/ProgressJob.vue';

const jobs = ref<Record<string, number>>({});
const duration: Ref<string | null> = ref(null);

async function startJob() {
  const response = await axios.post(route('test-job'), {
    duration: duration.value ? Number(duration.value) : null,
  });

  duration.value = null;

  jobs.value[response.data.identifier] = response.data.duration;
}
</script>
