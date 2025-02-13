<template>
  <Portal to="auth-header">
    {{ $t('pages.forgot_password.header') }}
  </Portal>

  <form @submit.prevent="requestPasswordEmail" class="space-y-6">
    <SimpleInput
      v-model="form.email"
      :label="$t('general.fields.email')"
      identifier="email"
      required
      :error="$page.props.status || null"
    />

    <button
      type="submit"
      class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
    >
      {{ $t('pages.forgot_password.submit') }}
    </button>
  </form>
</template>

<script setup lang="ts">
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { Portal } from 'portal-vue';
import SimpleInput from '../../Components/Input/SimpleInput.vue';
import { useForm } from '@inertiajs/vue3';

defineProps({
  status: {
    type: String,
    required: false,
    default: null,
  },
});

defineOptions({
  layout: AuthLayout,
});

const form = useForm({
  email: '',
});

function requestPasswordEmail() {
  form.post(route('password.email'), {
    preserveState: true,
    preserveScroll: true,
  });
}
</script>
