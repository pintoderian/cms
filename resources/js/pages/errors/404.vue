<template>
 <!-- begin error -->
	<div class="error">
		<div class="error-code m-b-10">404</div>
		<div class="error-content">
			<div class="error-message">{{ $t('page_not_found') }}</div>
			<div class="error-desc m-b-30">
				The page you're looking for doesn't exist. <br />
				Perhaps, there pages will help find what you're looking for.
			</div>
			<div>
        <router-link class="btn btn-success p-l-20 p-r-20" :to="{ name: 'dashboard' }">
          {{ $t('go_home') }}
        </router-link>
			</div>
		</div>
	</div>
</template>

<script>
import PageOptions from '~/config/PageOptions'
export default {
  name: 'NotFound',
  created() {
		PageOptions.pageEmpty = true;
  },
}
</script>
