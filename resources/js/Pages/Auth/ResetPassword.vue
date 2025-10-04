<template>
  <Portal to="auth-header">
    {{ $t('pages.reset_password.header') }}
  </Portal>

  <form @submit.prevent="submitForm" class="flex flex-col gap-4">
    <SimpleInput
      v-model="form.password"
      :error="usePage().props.errors.password || null"
      :label="$t('pages.reset_password.new_password.label')"
      identifier="password"
      required
      autocomplete="new-password"
      type="password"
    />

    <SimpleInput
      v-model="form.password_confirmation"
      :error="usePage().props.errors.password_confirmation || null"
      :label="$t('pages.reset_password.password_confirmation.label')"
      identifier="password_confirmation"
      required
      autocomplete="none"
      type="password"
    />

    <span v-if="usePage().props.errors.email" class="text-sm text-red-500">
      {{ usePage().props.errors.email }}
    </span>

    <button
      type="submit"
      class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
    >
      {{ $t('pages.reset_password.submit') }}
    </button>
  </form>
</template>

<script setup lang="ts">
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { Portal } from 'portal-vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SimpleInput from '../../Components/Input/SimpleInput.vue';

defineOptions({
  layout: AuthLayout,
});

const token = usePage().props.token;
const email = usePage().props.email;

const form = useForm({
  email: email,
  password: '',
  password_confirmation: '',
  token: token,
});

function submitForm() {
  form.post(route('password.update'));
}
</script>
