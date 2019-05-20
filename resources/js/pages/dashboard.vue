<template>
  <div>
    <div class="text-center">
      <div class="title mb-4">
        holaaaa
      </div>
    </div>
  </div>
</template>

<script>
import PageOptions from '~/config/PageOptions'
export default {
  middleware: 'auth',
  
  data: () => ({
    title: window.config.appName,
  }),

  created(){
    PageOptions.pageEmpty = false;
  }
}
</script>