<template>
  <div class="catalogs">
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
      <li class="breadcrumb-item">
        <router-link :to="{name:'dashboard'}">Inicio</router-link>
      </li>
      <li class="breadcrumb-item">
        <a href="javascript:;">Core</a>
      </li>
      <li class="breadcrumb-item active">Catálogos</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Catálogos</h1>
    <!-- end page-header -->
    <div class="row">
      <div class="col-md-12">
        <!-- begin panel -->
        <panel noButton="true">
          <template slot="header">
            <div class="pull-right title-btn">
              <b-btn
                  @click="view_modal('store')"
                  class="text-uppercase"
                  size="sm"
                  variant="success"
              >
                <i class="ti-plus"></i> Crear Catálogo
              </b-btn>
            </div>
            <h4 class="panel-title">Listado de catálogos</h4>
          </template>
          <vuetable
              :btn-editar="edit_data"
              :btn-elminar="true"
              :buscar="true"
              :buscar-mayus="false"
              :fields="fields"
              permiso_eliminar="delete_catalog"
              ref="CoreTable"
              url="/api/core/catalogs"
              :custom-buttons="custom_buttons"
          ></vuetable>
        </panel>
      </div>
    </div>
    <!-- Modal para crear y actualizar -->
    <b-modal :title="title" hide-footer id="FormModal" ref="FormModal">
      <b-form @submit.prevent="save_data()">
        <div class="d-block">
          <div class="form-group">
            <label>Nombre:</label>
            <b-form-input
                :class="{ 'is-invalid': form.errors.has('name') }"
                name="name"
                placeholder="Ingresar.."
                type="text"
                v-model="form.name"
            ></b-form-input>
            <has-error :form="form" field="name"/>
          </div>
        </div>
        <div class="text-right">
          <b-btn @click="cancel_modal()" variant="danger">Cancelar</b-btn>
          <b-btn type="submit" variant="success">Guardar</b-btn>
        </div>
      </b-form>
    </b-modal>
    <!-- fin del modal-->
  </div>
</template>

<script>
    import Form from "vform";
    import {mapGetters} from "vuex";
    import vuetable from "~/components/vuetable";

    export default {
        middleware: "auth",
        name: "catalogs",
        components: {
            vuetable
        },
        metaInfo() {
            return {title: "Catálogos"};
        },
        data() {
            return {
                fields: [
                    {key: "name", label: "Nombre"},
                    {key: "slug", label: "Slug"}
                ],
                form: new Form({
                    name: "",
                    id: ""
                }),
                type: null,
                title: "",
                custom_buttons: []
            };
        },
        computed: mapGetters({
            user: "auth/user"
        }),
        methods: {
            edit_data: function (data) {
                this.view_modal("update");
                if (this.type == "update") {
                    this.form.id = data.id;
                    this.form.name = data.name;
                }
            },
            cancel_modal: function () {
                this.$refs.FormModal.hide();
                this.clear_form();
            },
            view_modal: function (type = null) {
                if (type == "store") {
                    if (this.$permission(this.user.all_permissions, "add_roles")) {
                        this.type = "store";
                        this.title = "Agregar Catálogo";
                        this.clear_form();
                        this.$refs.FormModal.show();
                    } else {
                        this.$opps_error();
                    }
                } else if ("update") {
                    if (this.$permission(this.user.all_permissions, "edit_roles")) {
                        this.type = "update";
                        this.title = "Editar Catálogo";
                        this.$refs.FormModal.show();
                    } else {
                        this.$opps_error();
                    }
                }
            },
            save_data: function () {
                if (this.type == "store") {
                    this.store_data();
                } else {
                    this.update_data();
                }
            },
            store_data: function () {
                this.form.post("/api/core/catalogs").then(({data}) => {
                    this.cancel_modal();
                    this.$refs.CoreTable.vueTable();
                    this.$message("store");
                });
            },
            update_data: function () {
                this.form.put("/api/core/catalogs/" + this.form.id).then(({data}) => {
                    this.cancel_modal();
                    this.$refs.CoreTable.vueTable();
                    this.$message("update");
                });
            },
            clear_form: function () {
                this.form = new Form({
                    name: "",
                    id: ""
                });
            }
        },
        created() {
            var self = this;
            this.custom_buttons = [
                {
                    text: "Detalles del catálogo",
                    icon: "fa fa-eye",
                    link: function(item) {
                        self.$router.push({name:"settings.catalog_details", params: {'id': item.id}});
                    }
                }
            ];
        },
        mounted: function () {
            this.$check_view_perms(this.user.all_permissions, "view_catalog");
        }
    };
</script>