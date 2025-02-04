<template>
  <Portal to="login-header">
    {{ $t('pages.auth.forgot-password.header') }}
  </Portal>

  <form @submit.prevent="requestPasswordEmail" class="space-y-6">
    <SimpleInput
      v-model="email"
      :label="$t('general.fields.email')"
      identifier="email"
      required
    />
    {{ email }}
  </form>
</template>

<script setup lang="ts">
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { Portal } from 'portal-vue';
import SimpleInput from '../../Components/Input/SimpleInput.vue';
import { ref } from 'vue';

defineOptions({
  layout: AuthLayout,
});

const email = ref();
</script>
