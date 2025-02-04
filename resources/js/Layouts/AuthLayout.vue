<template>
  <main class="bg-50 h-full w-full">
    <div
      class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
    >
      <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <h2
          class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
        >
          <PortalTarget name="login-header" />
        </h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
        <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
          <slot />
        </div>
      </div>
    </div>
  </main>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { PortalTarget } from 'portal-vue';

export default defineComponent({
  components: { PortalTarget },
});
</script>
