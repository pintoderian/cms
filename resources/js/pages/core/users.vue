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
      <li class="breadcrumb-item active">Usuarios</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Usuarios</h1>
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
                <i class="ti-plus"></i> Crear Usuario
              </b-btn>
            </div>
            <h4 class="panel-title">Listado de usuarios</h4>
          </template>
          <vuetable
            ref="CoreTable"
            url="/api/core/users"
            :buscar="true"
            :buscar-mayus="false"
            :fields="fields"
            :btn-elminar="true"
            permiso_eliminar="delete_users"
            :btn-editar="edit_data"
            :btn-detalle="true"
            :detalle="detail_fields"
            :custom-filter="custom_filters"
            :custom-fields="custom_fields"
            :custom-buttons="custom_buttons"
          ></vuetable>
        </panel>
      </div>
    </div>
    <!-- Modal para crear y actualizar -->
    <b-modal id="FormModal" ref="FormModal" size="lg" hide-footer :title="title">
      <b-form @submit.prevent="save_data()">
        <div class="d-block">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>Nombre:</label>
                <b-form-input
                  autocomplete="off"
                  v-model="form.name"
                  name="name"
                  size="sm"
                  type="text"
                  :class="{ 'is-invalid': form.errors.has('name') }"
                ></b-form-input>
                <has-error :form="form" field="name"/>
              </div>
              <div class="form-group">
                <label>Email:</label>
                <b-form-input
                  v-model="form.email"
                  name="email"
                  autocomplete="off"
                  size="sm"
                  type="text"
                  :class="{ 'is-invalid': form.errors.has('email') }"
                ></b-form-input>
                <has-error :form="form" field="email"/>
              </div>
              <div class="form-group">
                <label>Usuario:</label>
                <b-form-input
                  autocomplete="off"
                  v-model="form.username"
                  size="sm"
                  name="username"
                  type="text"
                  :class="{ 'is-invalid': form.errors.has('username') }"
                ></b-form-input>
                <has-error :form="form" field="username"/>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>Contraseña:</label>
                <b-form-input
                  v-model="form.password"
                  name="password"
                  type="password"
                  size="sm"
                  :class="{ 'is-invalid': form.errors.has('password') }"
                ></b-form-input>
                <has-error :form="form" field="password"/>
              </div>
              <div class="form-group">
                <label>Confirmar Contraseña:</label>
                <b-form-input
                  v-model="form.password_confirmation"
                  name="password_confirmation"
                  type="password"
                  size="sm"
                  :class="{ 'is-invalid': form.errors.has('password_confirmation') }"
                ></b-form-input>
                <has-error :form="form" field="password_confirmation"/>
              </div>
              <div class="form-group">
                <label>Descripción:</label>
                <b-form-input
                  v-model="form.description"
                  size="sm"
                  name="description"
                  type="text"
                  autocomplete="off"
                  :class="{ 'is-invalid': form.errors.has('description') }"
                ></b-form-input>
                <has-error :form="form" field="description"/>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group" :class="{ 'is-invalid': form.errors.has('roles') }">
                <label>Roles/Perfiles:</label>
                <b-form-select
                  v-model="form.roles"
                  :options="roles"
                  :select-size="4"
                  multiple
                  name="roles"
                  value-field="id"
                  text-field="name"
                  :class="{ 'is-invalid': form.errors.has('roles') }"
                >
                  <template slot="first">
                    <option :value="null" disabled>-- Seleccionar los roles --</option>
                  </template>
                </b-form-select>
                <has-error :form="form" field="roles"/>
              </div>
            </div>
          </div>
        </div>
        <div class="text-right">
          <b-btn variant="danger" @click="cancel_modal()">Cancelar</b-btn>
          <b-btn variant="success" type="submit">Guardar</b-btn>
        </div>
      </b-form>
    </b-modal>
    <!--fin del modal-->
    <!-- modal para banear usuarios -->
    <b-modal id="BanModal" ref="BanModal" size="sm" hide-footer title="Ban/UnBan Usuario">
      <b-form @submit.prevent="ban_data()">
        <div class="d-block">
          <div
            class="alert alert-danger text-uppercase text-center"
            v-if="ban_status"
          >El usuario se encuentra baneado.</div>
          <div class="forms" v-if="!ban_status">
            <div class="form-group">
              <label>Fecha:</label>
              <b-form-input
                v-model="ban_user.date"
                name="date"
                type="date"
                size="sm"
                :class="{ 'is-invalid': ban_user.errors.has('date') }"
              ></b-form-input>
              <has-error :form="ban_user" field="date"/>
            </div>
            <div class="form-group">
              <label>Comentario:</label>
              <b-form-textarea
                v-model="ban_user.comment"
                name="comment"
                size="sm"
                :class="{ 'is-invalid': ban_user.errors.has('comment') }"
              ></b-form-textarea>
              <has-error :form="ban_user" field="comment"/>
            </div>
          </div>
        </div>
        <div class="text-right">
          <b-btn
            :variant="ban_status?'danger':'success'"
            type="submit"
          >{{ ban_status ? 'Desbanear' : 'Banear' }}</b-btn>
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
  name: "users",
  metaInfo() {
    return { title: "Usuarios" };
  },
  components: {
    vuetable
  },
  data() {
    return {
      fields: [
        { key: "name", label: "Nombre" },
        { key: "username", label: "Usuario", class: "text-center" },
        { key: "email", label: "Correo", class: "text-center" },
        { key: "ban", label: "Ban", class: "text-center" }
      ],
      detail_fields: [
        { title: "Descripcion", id: "description" },
        { title: "Roles", id: "roles", nameRow: "name" },
        { title: "Permisos", id: "all_permissions", nameRow: "index" }
      ],
      custom_fields: [
        {
          key: "ban",
          html: true,
          replace: function(data) {
            if (data == "SI") {
              return "<span class='badge badge-danger'>SI</span>";
            } else {
              return "<span class='badge badge-success'>NO</span>";
            }
          }
        }
      ],
      form: new Form({
        id: "",
        name: "",
        email: "",
        username: "",
        description: "",
        roles: []
      }),
      type: null,
      title: "",
      custom_filters: [],
      custom_buttons: [],
      roles: [],
      ban_status: false,
      ban_user: new Form({
        id: "",
        date: "",
        comment: "",
        type: ""
      })
    };
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  methods: {
    get_roles: function() {
      let self = this;
      axios
        .get("/api/core/roles?per_page=all")
        .then(function(response) {
          self.roles = response.data;
          self.custom_filters.push({
            name: "Rol",
            type: "select",
            data: self.roles,
            slug: "rol",
            nameSelect: "name",
            value: ""
          });
        })
        .catch(function(error) {
          console.log(error.response.data);
        });
    },
    edit_data: function(data) {
      this.view_modal("update");
      if (this.type == "update") {
        let roles = [];
        data.roles.forEach(element => {
          roles.push(element.id);
        });
        this.form = new Form({
          id: data.id,
          name: data.name,
          email: data.email,
          username: data.username,
          description: data.description,
          roles: roles
        });
      }
    },
    cancel_modal: function() {
      this.$refs.FormModal.hide();
      this.clear_form();
    },
    view_modal: function(type = null) {
      if (type == "store") {
        if (this.$permission(this.user.all_permissions, "add_users")) {
          this.type = "store";
          this.title = "Agregar Usuario";
          this.clear_form();
          this.$refs.FormModal.show();
        } else {
          this.$opps_error();
        }
      } else if ("update") {
        if (this.$permission(this.user.all_permissions, "edit_users")) {
          this.type = "update";
          this.title = "Editar Usuario";
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
      this.form.post("/api/core/users").then(({ data }) => {
        this.cancel_modal();
        this.$refs.CoreTable.vueTable();
        this.$message("store");
      });
    },
    update_data: function() {
      this.form.put("/api/core/users/" + this.form.id).then(({ data }) => {
        this.cancel_modal();
        this.$refs.CoreTable.vueTable();
        this.$message("update");
      });
    },
    clear_form: function() {
      this.form = new Form({
        id: "",
        name: "",
        email: "",
        username: "",
        description: "",
        roles: []
      });
    },
    ban_modal: function(item) {
      if (this.$permission(this.user.all_permissions, "edit_users")) {
        this.$refs.BanModal.show();
        this.ban_user.id = item.id;
        if (item.ban == "SI") {
          this.ban_status = true;
          this.ban_user.type = "unban";
        } else {
          this.ban_status = false;
          this.ban_user.type = "ban";
        }
      }
    },
    ban_clear: function() {
      this.ban_user = new Form({
        id: "",
        date: "",
        comment: "",
        type: ""
      });
    },
    ban_data: function() {
      this.ban_user.post("/api/core/ban-users").then(({ data }) => {
        this.$refs.BanModal.hide();
        this.ban_clear();
        this.$refs.CoreTable.vueTable();
        this.$message("update");
      });
    }
  },
  created() {
    this.get_roles();
    var self = this;
    this.custom_buttons = [
      {
        text: "Ban/UnBan",
        icon: "fa fa-eye",
        link: function(item) {
          self.ban_modal(item);
        }
      }
    ];
  },
  mounted: function() {
    this.$check_view_perms(this.user.all_permissions, "view_users");
  }
};
</script>