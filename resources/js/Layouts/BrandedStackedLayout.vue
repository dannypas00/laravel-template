<template>
  <div class="min-h-full">
    <Disclosure v-slot="{ open }" as="nav" class="bg-brand">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <img
                class="h-8 w-8"
                src="https://tailwindui.com/img/logos/mark.svg?color=red&shade=500"
                alt="Your Company"
              />
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <StackedNavEntry
                  v-for="item in navigation"
                  :key="item.name"
                  :item="item"
                />
              </div>
            </div>
          </div>
          <div class="hidden md:block">
            <div class="ml-4 flex items-center md:ml-6">
              <button
                type="button"
                class="relative rounded-full bg-brand p-1 text-brand-light hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand"
              >
                <span class="absolute -inset-1.5" />
                <span
                  v-t="'components.layout.view_notifications'"
                  class="sr-only"
                />
                <BellIcon class="h-6 w-6" aria-hidden="true" />
              </button>

              <!-- Profile dropdown -->
              <Menu as="div" class="relative ml-3">
                <div>
                  <MenuButton
                    class="relative flex max-w-xs items-center rounded-full bg-brand text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand"
                  >
                    <span class="absolute -inset-1.5" />
                    <span
                      v-t="'components.layout.open_user_menu'"
                      class="sr-only"
                    />
                    <img
                      class="h-8 w-8 rounded-full"
                      :src="user.profile_photo_url"
                      alt=""
                    />
                  </MenuButton>
                </div>
                <transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <MenuItems
                    class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                  >
                    <MenuItem
                      v-for="item in userNavigation"
                      :key="item.name"
                      v-slot="{ active }"
                    >
                      <a
                        :href="$route(item.route)"
                        :class="[
                          active ? 'bg-gray-100' : '',
                          'block px-4 py-2 text-sm text-gray-700',
                        ]"
                      >
                        {{ item.name }}
                      </a>
                    </MenuItem>
                  </MenuItems>
                </transition>
              </Menu>
            </div>
          </div>
          <div class="-mr-2 flex md:hidden">
            <!-- Mobile menu button -->
            <DisclosureButton
              class="relative inline-flex items-center justify-center rounded-md bg-brand p-2 text-brand-light hover:bg-brand-hover hover:bg-opacity-75 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand"
            >
              <span class="absolute -inset-0.5" />
              <span v-t="'components.layout.open_main_menu'" class="sr-only" />
              <Bars3Icon
                v-if="!open"
                class="block h-6 w-6"
                aria-hidden="true"
              />
              <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
            </DisclosureButton>
          </div>
        </div>
      </div>

      <DisclosurePanel class="md:hidden">
        <div class="space-y-1 px-2 pb-3 pt-2 sm:px-3">
          <StackedDisclosureNavEntry
            v-for="item in navigation"
            :key="item.name"
            :item="item"
            @select="open = false"
          />
        </div>
        <div class="border-t border-brand-dark pb-3 pt-4">
          <div class="flex items-center px-5">
            <div class="flex-shrink-0">
              <img
                class="h-10 w-10 rounded-full"
                :src="user.profile_photo_url"
                alt=""
              />
            </div>
            <div class="ml-3">
              <div class="text-base font-medium text-white">
                {{ user.name }}
              </div>
              <div class="text-sm font-medium text-brand-light">
                {{ user.email }}
              </div>
            </div>
            <button
              type="button"
              class="relative ml-auto flex-shrink-0 rounded-full bg-brand p-1 text-brand-light hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-brand"
            >
              <span class="absolute -inset-1.5" />
              <span
                v-t="'components.layout.view_notifications'"
                class="sr-only"
              />
              <BellIcon class="h-6 w-6" aria-hidden="true" />
            </button>
          </div>
          <div class="mt-3 space-y-1 px-2">
            <StackedDisclosureNavEntry
              v-for="item in userNavigation"
              :key="item.name"
              :item="item"
            />
          </div>
        </div>
      </DisclosurePanel>
    </Disclosure>

    <header class="bg-white shadow-sm">
      <PortalTarget name="layout-header" />
    </header>
    <main>
      <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from '@headlessui/vue';
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline';
import { layoutNavigationItems, userNavigationItems } from '../LayoutConfig';
import StackedNavEntry from './Partials/StackedNavEntry.vue';
import StackedDisclosureNavEntry from './Partials/StackedDisclosureNavEntry.vue';
import { usePage } from '@inertiajs/vue3';
import { UserData } from '../Types/generated';
import { PortalTarget } from 'portal-vue';

const user: UserData = usePage().props.user;
const navigation = layoutNavigationItems;
const userNavigation = userNavigationItems;
</script>
