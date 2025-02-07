<template>
  <Portal to="auth-header">
    {{ $t('pages.verify_email.header') }}
  </Portal>

  <div class="space-y-4">
    <p class="text-center text-slate-800">
      {{ $t('pages.verify_email.info') }}
    </p>

    <form @submit.prevent="resendVerificationEmail">
      <button
        type="submit"
        class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand disabled:bg-gray-500 disabled:outline-gray-700"
        :disabled="buttonDisabled"
      >
        {{ $t('pages.verify_email.resend') }}
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';
import { Portal } from 'portal-vue';
import { ref } from 'vue';

defineOptions({
  layout: AuthLayout,
});

const buttonDisabled = ref(false);

function resendVerificationEmail() {
  router.post(
    route('verification.send'),
    {
      preserveState: true,
      preserveScroll: true,
    },
    {
      onStart() {
        buttonDisabled.value = true;
      },
      onFinish() {
        setTimeout(() => {
          buttonDisabled.value = false;
        }, 10000);
      },
    }
  );
}
</script>
