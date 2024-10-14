<template>
  <PageHeader :header="$t('pages.formkit.title')"/>

  <FormKit type="text" name="name" id="name" :label="$t('pages.formkit.form.name.label')"/>
</template>

<script setup lang="ts">
import PageHeader from '../../Components/Layout/PageHeader.vue';

</script>
