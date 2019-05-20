<template>
  <ul class="nav">
    <li class="nav-profile" v-if="user">
      <a href="javascript:;" v-on:click="expand()">
        <div class="cover with-shadow"></div>
        <div class="image image-icon bg-black text-grey-darker">
          <i class="fa fa-user"></i>
        </div>
        <div class="info">
          <b class="caret pull-right"></b>
          {{ user.name }}
          <small>Sys Admin</small>
        </div>
      </a>
    </li>
    <li>
      <ul class="nav nav-profile" v-bind:class="{ 'd-block': this.stat == 'expand', 'd-none': this.stat == 'collapse' }">
        <li>
          <router-link :to="{ name: 'settings.profile' }"><i class="fa fa-cog"></i> {{ $t("settings") }}</router-link>
        </li>
        <!--<li>
          <a href="javascript:;"><i class="fa fa-pencil-alt"></i> Send Feedback</a>
        </li>
        <li>
          <a href="javascript:;"><i class="fa fa-question-circle"></i> Helps</a>
        </li>-->
      </ul>
    </li>
  </ul>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  name: "SidebarNavProfile",
  data() {
    return {
      stat: ""
    };
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  methods: {
    expand: function() {
      this.stat = this.stat == "expand" ? "collapse" : "expand";
    }
  }
};
</script>
