<template>
  <div>
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
      <li class="breadcrumb-item">
        <router-link :to="{name:'dashboard'}">Inicio</router-link>
      </li>
      <li class="breadcrumb-item">
        <a href="javascript:;">Core</a>
      </li>
      <li class="breadcrumb-item">Logs</li>
      <li class="breadcrumb-item active">Acceso de usuarios</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Acceso de usuarios</h1>
    <!-- end page-header -->

    <div class="row">
      <div class="col-md-12">
        <!-- begin panel -->
        <panel noButton="true">
          <template slot="header">
            <h4 class="panel-title">Listado de acceso de usuarios</h4>
          </template>
          <vuetable
            ref="CoreTable"
            url="/api/core/logs/user-access"
            :buscar="false"
            :fields="fields"
            :btn-enabled="false"
            :custom-fields="custom_fields"
            :custom-filter="custom_filters"
            :btn-detalle="true"
            :detalle="detail_fields"
          ></vuetable>
        </panel>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import vuetable from "~/components/vuetable";
export default {
  middleware: "auth",
  name: "user_access",
  components: {
    vuetable
  },
  metaInfo() {
    return { title: "Acceso de usuarios" };
  },
  data() {
    return {
      fields: [
        { key: "id", label: "ID", class: "text-center" },
        { key: "user.name", label: "USUARIO", class: "text-center" },
        { key: "ip_address", label: "IP", class: "text-center" },
        { key: "login_at", label: "FECHA ACCESO", class: "text-center" },
        { key: "logout_at", label: "FECHA SALIDA", class: "text-center" }
      ],
      custom_fields: [
        {
          key: "ip_address",
          html: true,
          replace: "<span class='badge badge-dark'>:data</span>"
        },
        {
          key: "login_at",
          html: true,
          replace: "<span class='badge badge-warning'>:data</span>"
        },
        {
          key: "logout_at",
          html: true,
          replace: "<span class='badge badge-danger'>:data</span>"
        }
      ],
      detail_fields: [
        { title: "Detalles", id: "browser" },
        { title: "User Agent", id: "user_agent" }
      ],
      custom_filters: [
        {
          name: "Fecha",
          type: "date",
          slug: "fecha",
          value: ""
        }
      ],
      users: []
    };
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  methods: {
    get_users: function() {
      let self = this;
      axios
        .get("/api/core/select/users")
        .then(function(response) {
          self.users = response.data;
          self.custom_filters.push({
            name: "Usuario",
            type: "select",
            data: self.users,
            slug: "usuario",
            nameSelect: "text",
            value: ""
          });
        })
        .catch(function(error) {
          console.log(error.response.data);
        });
    }
  },
  created() {
    this.get_users();
  },
  mounted: function() {
    this.$check_view_perms(this.user.all_permissions, "logs_user_access");
  }
};
</script>