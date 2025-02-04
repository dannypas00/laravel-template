<template>
  <Portal to="login-header">
    {{ $t('pages.auth.login.header') }}
  </Portal>

  <div class="border-b border-gray-200">
    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
      <a
        v-for="(tab, mode) in tabs"
        :key="tab.name"
        class="cursor-pointer select-none"
        :class="[
          selectedMode === mode
            ? 'border-brand-hover text-brand'
            : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
          'whitespace-nowrap border-b-2 px-1 py-4 text-sm font-medium',
        ]"
        :aria-current="selectedMode === mode ? 'page' : undefined"
        @click="() => changeMode(mode)"
      >
        {{ tab.name }}
      </a>
    </nav>
  </div>

  <form
    class="mt-4 space-y-4"
    @submit.prevent="() => (selectedMode === 'login' ? login() : register())"
  >
    <SimpleInput
      v-model="form.name"
      v-if="selectedMode === 'register'"
      :label="$t('pages.auth.login.name.label')"
      autocomplete="username"
      identifier="name"
      required
    />

    <SimpleInput
      v-model="form.email"
      :label="$t('pages.auth.login.email.label')"
      type="email"
      autocomplete="email"
      identifier="email"
      required
    />

    <SimpleInput
      v-model="form.password"
      :label="$t('pages.auth.login.password.label')"
      type="password"
      :autocomplete="selectedMode === 'login' ? 'current-password' : 'none'"
      identifier="password"
      required
    />

    <SimpleInput
      v-if="selectedMode === 'register'"
      v-model="form.password_confirmation"
      :label="$t('pages.auth.login.password_confirmation.label')"
      type="password"
      autocomplete="none"
      identifier="password_confirmation"
      required
    />

    <div v-else class="flex items-center justify-between">
      <div class="flex items-center">
        <input
          id="remember-me"
          v-model="form.rememberMe"
          name="remember-me"
          type="checkbox"
          class="h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand"
        />
        <label
          v-t="'pages.auth.login.remember_me.label'"
          for="remember-me"
          class="ml-3 block text-sm leading-6 text-gray-900"
        />
      </div>

      <div class="text-sm leading-6">
        <Link
          class="text font-semibold text-brand hover:text-brand-hover"
          as="a"
          :href="route('password.request')"
        >
          {{ $t('pages.auth.login.forgot_password') }}
        </Link>
      </div>
    </div>

    <div v-if="selectedMode === 'login'">
      <button
        type="submit"
        class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
      >
        {{ $t('pages.auth.login.submit_button') }}
      </button>
    </div>

    <div v-else>
      <button
        type="submit"
        class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
      >
        {{ $t('pages.auth.login.register_button') }}
      </button>
    </div>
  </form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import I18n from '../../i18n';
import { Portal } from 'portal-vue';
import SimpleInput from '../../Components/Input/SimpleInput.vue';

defineOptions({
  layout: AuthLayout,
});

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  rememberMe: false,
});

const tabs = {
  login: { name: I18n.global.t('pages.auth.login.login_tab') },
  register: { name: I18n.global.t('pages.auth.login.register_tab') },
};

let selectedMode = ref('login');

function changeMode(mode: string) {
  selectedMode.value = mode;
}

function login() {
  form
    .transform(data => ({
      email: data.email,
      password: data.password,
      rememberMe: data.rememberMe ? 'on' : null,
    }))
    .post(route('login'));
}

function register() {
  form
    .transform(data => ({
      name: data.name,
      email: data.email,
      password: data.password,
      password_confirmation: data.password_confirmation,
    }))
    .post(route('register'));
}
</script>
