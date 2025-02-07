<template>
  <Portal to="auth-header">
    {{ $t('pages.reset_password.header') }}
  </Portal>

  <form @submit.prevent="submitForm">
    <SimpleInput
      v-model="form.password"
      identifier="password"
      required
      :error="usePage().props.errors.password || null"
      :label="$t('pages.reset_password.new_password.label')"
      autocomplete="new-password"
    />
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

const token = usePage().token;
const email = usePage().token;

const form = useForm({
  email: email,
  password: '',
  token: token,
});

function submitForm() {
  form.post(route('password.update'));
}

console.log(usePage());
</script>
