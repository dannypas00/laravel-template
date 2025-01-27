<template>
  <div
    class="flex min-h-full flex-1 flex-col justify-center py-12 sm:px-6 lg:px-8"
  >
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
      <h2
        v-t="'pages.auth.login.header'"
        class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900"
      />
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
      <div class="bg-white px-6 py-12 shadow sm:rounded-lg sm:px-12">
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
          class="mt-4 space-y-6"
          @submit.prevent="
            () => (selectedMode === 'login' ? login() : register())
          "
        >
          <div v-if="selectedMode === 'register'">
            <label
              v-t="'pages.auth.login.name.label'"
              for="name"
              class="block text-sm font-medium leading-6 text-gray-900"
            />
            <div class="mt-2">
              <input
                id="name"
                v-model="form.name"
                name="name"
                type="text"
                autocomplete="username"
                required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6"
              />
              <span
                v-if="$page.props.errors?.name"
                class="text-sm text-red-500"
                >{{ $page.props.errors?.name }}</span
              >
            </div>
          </div>

          <div>
            <label
              v-t="'pages.auth.login.email.label'"
              for="email"
              class="block text-sm font-medium leading-6 text-gray-900"
            />
            <div class="mt-2">
              <input
                id="email"
                v-model="form.email"
                name="email"
                type="email"
                autocomplete="email"
                required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6"
              />
              <span
                v-if="$page.props.errors?.email"
                class="text-sm text-red-500"
                >{{ $page.props.errors?.email }}</span
              >
            </div>
          </div>

          <div>
            <label
              v-t="'pages.auth.login.password.label'"
              for="password"
              class="block text-sm font-medium leading-6 text-gray-900"
            />
            <div class="mt-2">
              <input
                id="password"
                v-model="form.password"
                :autocomplete="
                  selectedMode === 'login' ? 'current-password' : 'none'
                "
                name="password"
                type="password"
                required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6"
              />
              <span
                v-if="$page.props.errors?.password"
                class="text-sm text-red-500"
                >{{ $page.props.errors?.password }}</span
              >
            </div>
          </div>

          <div v-if="selectedMode === 'register'">
            <label
              v-t="'pages.auth.login.password_confirmation.label'"
              for="password_confirmation"
              class="block text-sm font-medium leading-6 text-gray-900"
            />
            <div class="mt-2">
              <input
                id="password_confirmation"
                v-model="form.password_confirmation"
                name="password_confirmation"
                type="password"
                autocomplete="none"
                required
                class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-brand sm:text-sm sm:leading-6"
              />
            </div>
          </div>

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
              <a
                class="text font-semibold text-brand hover:text-brand-hover"
                @click="forgotPassword"
              >
                {{ $t('pages.auth.login.forgot_password') }}
              </a>
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
              class="flex w-full justify-center rounded-md bg-brand px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-brand-hover focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand"
              @click="register"
            >
              {{ $t('pages.auth.login.register_button') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import AuthLayout from '../../Layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';

export default defineComponent({
  layout: AuthLayout,

  data() {
    return {
      form: useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        rememberMe: false,
      }),
      tabs: {
        login: { name: this.$t('pages.auth.login.login_tab') },
        register: { name: this.$t('pages.auth.login.register_tab') },
      },
      selectedMode: 'login',
    };
  },

  methods: {
    changeMode(mode: string) {
      this.selectedMode = mode;
    },

    login() {
      this.form
        .transform(data => ({
          email: data.email,
          password: data.password,
          rememberMe: data.rememberMe ? 'on' : null,
        }))
        .post(route('login'));
    },

    register() {
      this.form
        .transform(data => ({
          name: data.name,
          email: data.email,
          password: data.password,
          password_confirmation: data.password_confirmation,
        }))
        .post(route('register'));
    },

    forgotPassword() {
      // TODO
      console.log('To be implemented');
    },
  },
});
</script>
