<template>
  <PageHeader
    :title="$t('pages.page1.title')"
    :header="$t('pages.page1.title')"
  >
    <IconButton
      :text="$t('pages.page1.header.new_button')"
      :icon="{ icon: 'fas fa-plus' }"
      class="bg-green-600 text-white hover:bg-green-500 focus-visible:outline-green-600"
      @click="createModalOpen = true"
    />
  </PageHeader>

  <QueryBuilderTable
    ref="table"
    v-model:selected="selected"
    :request="request"
    :headers="getTableHeaders()"
    :bulk-actions="getBulkOptions()"
    selectable
    auto-search
  />

  <ModalDialog v-model:open="createModalOpen" title="Create new user">
    <template #content>
      <UserCreateForm @submit="onUserSubmit" />
    </template>
  </ModalDialog>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import QueryBuilderTable from '../../Components/DataTable/QueryBuilderTable.vue';
import { UserIndexRequest } from '../../Communication/Users/UserIndexRequest';
import {
  BulkOption,
  FilterType,
  getCreatedAtHeader,
  getIdHeader,
  getUpdatedAtHeader,
  TableHeader,
} from '../../Components/DataTable/DataTableTypes';
import PageHeader from '../../Components/Layout/PageHeader.vue';
import { UserData } from '../../Types/generated';
import { faTrash } from '@fortawesome/free-solid-svg-icons';
import ModalDialog from '../../Components/Dialogs/ModalDialog.vue';
import IconButton from '../../Components/Buttons/IconButton.vue';
import UserCreateForm from './Partials/UserCreateForm.vue';
import {
  UserCreateData,
  UserCreateRequest,
} from '../../Communication/Users/UserCreateRequest';

export default defineComponent({
  components: {
    UserCreateForm,
    IconButton,
    ModalDialog,
    QueryBuilderTable,
    PageHeader,
  },

  data() {
    return {
      request: new UserIndexRequest(),
      userCreateRequest: new UserCreateRequest(),
      selected: [],
      createModalOpen: false,
    };
  },

  methods: {
    getTableHeaders(): TableHeader<UserData>[] {
      return [
        getIdHeader<UserData>(),
        {
          key: 'name',
          title: this.$t('pages.page1.table.name_title'),
          sortable: true,
          filter: {
            type: FilterType.Search,
            filter: 'name',
            placeholder: this.$t('pages.page1.table.name_placeholder'),
          },
        },
        {
          key: 'email',
          title: this.$t('pages.page1.table.email_title'),
          filter: {
            type: FilterType.Search,
            filter: 'email',
            placeholder: this.$t('pages.page1.table.email_placeholder'),
          },
        },
        getCreatedAtHeader<UserData>(),
        getUpdatedAtHeader<UserData>(),
      ];
    },

    getBulkOptions(): BulkOption<UserData>[] {
      return [
        {
          title: 'Delete',
          onClick: (selected: UserData[]) => {
            console.log('Clicked bulk delete!', selected);
          },
          unselectAfter: true,
          icon: {
            icon: faTrash,
          },
          classes: 'bg-red-500 hover:bg-red-700 border-red-400 text-white',
          confirmation: true,
          confirmationText: (selected: UserData[]) =>
            this.$t('pages.page1.table.delete_text', selected),
        },
        {
          title: 'Test',
          unselectAfter: false,
        },
      ] as BulkOption<UserData>[];
    },

    async onUserSubmit(data: UserCreateData) {
      this.createModalOpen = false;
      await this.userCreateRequest.setData(data).getResponse();
      this.$refs.table.getData();
    },
  },
});
</script>
