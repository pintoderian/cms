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
      <li class="breadcrumb-item active">Actividad de usuarios</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Actividad de usuarios</h1>
    <!-- end page-header -->

    <div class="row">
      <div class="col-md-12">
        <!-- begin panel -->
        <panel noButton="true">
          <template slot="header">
            <h4 class="panel-title">Listado de actividad de usuarios</h4>
          </template>
          <vuetable
            ref="CoreTable"
            url="/api/core/logs/user-activity"
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
  name: "user_activity",
  components: {
    vuetable
  },
  metaInfo() {
    return { title: "Actividad de usuarios" };
  },
  data() {
    return {
      fields: [
        { key: "id", label: "ID", class: "text-center text-uppercase" },
        {
          key: "user.name",
          label: "Usuario",
          class: "text-center text-uppercase"
        },
        {
          key: "current_date_b",
          label: "Fecha",
          class: "text-center text-uppercase"
        },
        { key: "ip_address", label: "IP", class: "text-center text-uppercase" },
        {
          key: "type_query",
          label: "Tipo de SQL",
          class: "text-center text-uppercase"
        },
        {
          key: "table_name",
          label: "Nombre de tabla",
          class: "text-center text-uppercase"
        }
      ],
      custom_fields: [
        {
          key: "ip_address",
          html: true,
          replace: "<span class='badge badge-dark'>:data</span>"
        },
        {
          key: "type_query",
          html: true,
          replace: "<span class='badge badge-success'>:data</span>"
        },
        {
          key: "table_name",
          html: true,
          replace: "<span class='badge badge-danger'>:data</span>"
        }
      ],
      detail_fields: [
        { title: "Detalles", id: "navegador" },
        { title: "User Agent", id: "user_agent" },
        { title: "Url", id: "current_url" },
        { title: "SQL", id: "sql_query" }
      ],
      custom_filters: [
        {
          name: "Fecha",
          type: "date",
          slug: "fecha",
          value: ""
        },
        {
          name: "Tipo Sql",
          type: "select",
          data: [
            {
              text: "INSERT",
              id: "INSERT"
            },
            {
              text: "UPDATE",
              id: "UPDATE"
            },
            {
              text: "DELETE",
              id: "DELETE"
            }
          ],
          slug: "type_query",
          nameSelect: "text",
          value: ""
        }
      ],
      tables: [],
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
    },
    get_tables: function() {
      let self = this;
      axios
        .get("/api/core/select/tables")
        .then(function(response) {
          self.tables = response.data;
          self.custom_filters.push({
            name: "Tabla",
            type: "select",
            data: self.tables,
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
    this.get_tables();
    this.get_users();
  },
  mounted: function() {
    this.$check_view_perms(this.user.all_permissions, "logs_user_access");
  }
};
</script>