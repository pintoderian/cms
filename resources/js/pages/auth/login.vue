<template>
  <!-- begin login -->
  <div class="login bg-black animated fadeInDown">
    <!-- begin brand -->
    <div class="login-header">
      <div class="brand">
        <span class="logo"></span>
        <b>CodeGea</b> Admin
        <small>BEST CMS</small>
      </div>
      <div class="icon">
        <i class="fa fa-lock"></i>
      </div>
    </div>
    <!-- end brand -->
    <!-- begin login-content -->
    <div class="login-content">
      <form @submit.prevent="login" @keydown="form.onKeydown($event)">
        <div class="form-group m-b-20">
          <input
            v-model="form.username"
            :class="{ 'is-invalid': form.errors.has('username') }"
            :placeholder="$t('username')"
            class="form-control form-control-lg inverse-mode"
            type="text"
            name="username"
            autocomplete="off"
          >
          <has-error :form="form" field="username"/>
        </div>
        <div class="form-group m-b-20">
          <input
            v-model="form.password"
            :class="{ 'is-invalid': form.errors.has('password') }"
            :placeholder="$t('password')"
            class="form-control form-control-lg inverse-mode"
            type="password"
            name="password"
          >
          <has-error :form="form" field="password"/>
        </div>
        <div class="checkbox checkbox-css m-b-20">
          <input type="checkbox" v-model="remember" name="remember" id="remember_checkbox">
          <label for="remember_checkbox">{{ $t('remember_me') }}</label>
        </div>
        <div class="login-buttons">
          <v-button
            :loading="form.busy"
            type="success"
            class="text-uppercase btn-block"
          >{{ $t('login') }}</v-button>
        </div>
        <router-link
          :to="{ name: 'password.request' }"
          class="small ml-auto my-auto"
        >{{ $t('forgot_password') }}</router-link>
        <login-with-github/>
      </form>
    </div>
    <!-- end login-content -->
  </div>
  <!-- end login -->
</template>

<script>
import Form from "vform";
import LoginWithGithub from "~/components/LoginWithGithub";
import PageOptions from "~/config/PageOptions";
export default {
  middleware: "guest",

  components: {
    LoginWithGithub
  },

  metaInfo() {
    return { title: this.$t("login") };
  },

  data: () => ({
    form: new Form({
      username: "",
      password: ""
    }),
    remember: false
  }),

  methods: {
    async login() {
      // Submit the form.
      const { data } = await this.form.post("/api/login");

      // Save the token.
      this.$store.dispatch("auth/saveToken", {
        token: data.token,
        remember: this.remember
      });

      // Fetch the user.
      await this.$store.dispatch("auth/fetchUser");

      // Redirect home.
      this.$router.push({ name: "dashboard" });
    }
  },
  created() {
    PageOptions.pageEmpty = true;
  }
};
</script>
