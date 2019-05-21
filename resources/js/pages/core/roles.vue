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
      <li class="breadcrumb-item active">Roles</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Roles</h1>
    <!-- end page-header -->

    <div class="row">
      <div class="col-md-12">
        <!-- begin panel -->
        <panel noButton="true">
          <template slot="header">
            <div class="pull-right title-btn">
              <b-btn
                class="text-uppercase"
                variant="success"
                size="sm"
                @click="view_modal('store')"
              >
                <i class="ti-plus"></i> Crear Rol
              </b-btn>
            </div>
            <h4 class="panel-title">Listado de roles</h4>
          </template>
          <vuetable
            ref="CoreTable"
            url="/api/core/roles"
            :buscar="true"
            :buscar-mayus="false"
            :fields="fields"
            :btn-elminar="true"
            permiso_eliminar="delete_roles"
            :btn-editar="edit_data"
            :custom-buttons="custom_buttons"
          ></vuetable>
        </panel>
      </div>
    </div>
    <!-- Modal para crear y actualizar -->
    <b-modal id="FormModal" ref="FormModal" hide-footer :title="title">
      <b-form @submit.prevent="save_data()">
        <div class="d-block">
          <div class="form-group">
            <label>Nombre:</label>
            <b-form-input
              v-model="form.name"
              name="name"
              type="text"
              placeholder="Ingresar.."
              :class="{ 'is-invalid': form.errors.has('name') }"
            ></b-form-input>
            <has-error :form="form" field="name"/>
          </div>
        </div>
        <div class="text-right">
          <b-btn variant="danger" @click="cancel_modal()">Cancelar</b-btn>
          <b-btn variant="success" type="submit">Guardar</b-btn>
        </div>
      </b-form>
    </b-modal>
    <!-- fin del modal-->
    <!--cambiar permisos-->
    <b-modal id="PermsModal" ref="PermsModal" hide-footer title="Permisos" size="lg">
      <b-form @submit.prevent="change_perms">
        <div class="d-block">
          <b-form-group label="Todos los permisos:">
            <b-form-checkbox-group
              id="permissions"
              name="permisos"
              v-model="perms.result.data"
              :options="perms.result.permisos"
            ></b-form-checkbox-group>
          </b-form-group>
        </div>
        <div class="text-right">
          <b-btn variant="danger" @click="$refs.PermsModal.hide()">Cancelar</b-btn>
          <b-btn variant="success" type="submit">Guardar</b-btn>
        </div>
      </b-form>
    </b-modal>
    <!--fin del modal-->
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import vuetable from "~/components/vuetable";
export default {
  middleware: "auth",
  name: "roles",
  components: {
    vuetable
  },
  metaInfo() {
    return { title: "Roles" };
  },
  data() {
    return {
      fields: [{ key: "name", label: "Nombre" }],
      form: new Form({
        name: "",
        id: ""
      }),
      type: null,
      title: "",
      perms: {
        id: "",
        result: {}
      },
      custom_buttons: []
    };
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  methods: {
    edit_data: function(data) {
      this.view_modal("update");
      if (this.type == "update") {
        this.form.id = data.id;
        this.form.name = data.name;
      }
    },
    cancel_modal: function() {
      this.$refs.FormModal.hide();
      this.clear_form();
    },
    view_modal: function(type = null) {
      if (type == "store") {
        if (this.$permission(this.user.all_permissions, "add_roles")) {
          this.type = "store";
          this.title = "Agregar Rol";
          this.clear_form();
          this.$refs.FormModal.show();
        } else {
          this.$opps_error();
        }
      } else if ("update") {
        if (this.$permission(this.user.all_permissions, "edit_roles")) {
          this.type = "update";
          this.title = "Editar Rol";
          this.$refs.FormModal.show();
        } else {
          this.$opps_error();
        }
      }
    },
    save_data: function() {
      if (this.type == "store") {
        this.store_data();
      } else {
        this.update_data();
      }
    },
    store_data: function() {
      this.form.post("/api/core/roles").then(({ data }) => {
        this.cancel_modal();
        this.$refs.CoreTable.vueTable();
        this.$message("store");
      });
    },
    update_data: function() {
      this.form.put("/api/core/roles/" + this.form.id).then(({ data }) => {
        this.cancel_modal();
        this.$refs.CoreTable.vueTable();
        this.$message("update");
      });
    },
    clear_form: function() {
      this.form = new Form({
        name: "",
        id: ""
      });
    },
    view_perms: function(id) {
      if (this.$permission(this.user.all_permissions, "edit_roles")) {
        this.perms.id = id;
        var self = this;
        axios
          .get("/api/core/roles/" + id)
          .then(function(response) {
            self.perms.result = response.data;
            self.$refs.PermsModal.show();
          })
          .catch(function(error) {
            console.log(error.response.data);
          });
      } else {
        this.$opps_error();
      }
    },
    change_perms: function() {
      var self = this;
      axios
        .post("/api/core/roles/" + this.perms.id + "/perms", {
          perms: this.perms
        })
        .then(function(response) {
          self.$refs.PermsModal.hide();
          self.perms = {
            id: "",
            result: {}
          };
          self.$message("update");
        })
        .catch(function(error) {
          console.log(error.response.data);
        });
    }
  },
  created() {
    var self = this;
    this.custom_buttons = [
      {
        text: "Permisos",
        icon: "fa fa-eye",
        link: function(item) {
          self.view_perms(item.id);
        }
      }
    ];
  },
  mounted: function() {
    this.$check_view_perms(this.user.all_permissions, "view_roles");
  }
};
</script>