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
      <li class="breadcrumb-item active">Logs del sistema</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">Logs del sistema</h1>
    <!-- end page-header -->

    <div class="row">
      <div class="col-md-12">
        <!-- begin panel -->
        <panel noButton="true">
          <template slot="header">
            <h4 class="panel-title">Logs</h4>
          </template>
          <loader v-if="loading"></loader>
          <div id="logsTable" v-else>
            <div class="table-responsive">
              <table class="table table-sm table-hover">
                <thead>
                  <tr>
                    <th
                      v-for="(header, key) in datos.headers"
                      :key="key"
                      scope="col"
                      :class="header.class"
                    >
                      <span class="badge badge-info" v-if="header.key=='date'">{{ header.title }}</span>
                      <span :class="'badge badge-level-'+header.key" v-else>
                        <i :class="header.icon"></i>
                        {{ header.title }}
                      </span>
                    </th>
                    <th scope="col" class="text-right">Acciones</th>
                  </tr>
                </thead>
                <tbody v-if="datos.rows">
                  <tr v-for="(rows, key) in datos.rows.data" :key="key">
                    <td v-for="(row, i) in rows" :key="i" :class="i == 'date' ? '': 'text-center'">
                      <span class="badge badge-primary" v-if="i == 'date'">{{ row }}</span>
                      <span class="badge empty" v-else-if="row == 0">{{ row }}</span>
                      <router-link
                        :to="{ name: 'logs.system.view',  params:{ date:key, type:i } }"
                        v-else
                      >
                        <span :class="'badge badge-level-'+i">{{ row }}</span>
                      </router-link>
                    </td>
                    <td class="text-right">
                      <router-link
                        class="btn btn-info btn-sm waves-effect waves-light"
                        :to="{ name: 'logs.system.view',  params:{ date:key, type:'all' } }"
                      >
                        <i class="fa fa-search"></i>
                      </router-link>
                      <button
                        @click="download(key)"
                        type="button"
                        class="btn btn-primary btn-sm waves-effect waves-light"
                      >
                        <i class="fa fa-download"></i>
                      </button>
                      <button
                        @click="remove(key)"
                        type="button"
                        class="btn btn-danger btn-sm waves-effect waves-light"
                      >
                        <i class="fa fa-times"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <b-pagination size="sm" :total-rows="total" v-model="currentPage" :per-page="perPage"></b-pagination>
            <div class="clear"></div>
          </div>
        </panel>
      </div>
    </div>
  </div>
</template>

<script>
import swal from "sweetalert2";
import { mapGetters } from "vuex";
import loader from "~/components/loader";
export default {
  middleware: "auth",
  name: "logs_system",
  components: {
    loader
  },
  metaInfo() {
    return { title: "Logs del sistema" };
  },
  data() {
    return {
      datos: {
        rows: {
          data: {}
        }
      },
      currentPage: 1,
      total: 0,
      loading: true,
      perPage: 0
    };
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  watch: {
    currentPage: function(val) {
      this.get_data();
    }
  },
  methods: {
    get_data: function() {
      let self = this;
      self.loading = true;
      axios
        .get("/api/core/logs/system?page=" + self.currentPage)
        .then(function(response) {
          self.datos = response.data;
          self.total = self.datos.rows.total;
          self.perPage = self.datos.rows.per_page;
          self.loading = false;
        })
        .catch(function(error) {
          console.log(error.response.data);
        });
    },
    download: function(fecha) {
      window.open("/api/core/logs/system/" + fecha + "/download", "_blank");
    },
    remove: function(fecha) {
      let self = this;
      swal({
        title: "¿Ésta seguro de eliminar?",
        text: "Toda la información relacionada sera eliminada",
        type: "warning",
        showCancelButton: true,
        confirmButtonText: "Si, eliminar!",
        cancelButtonText: "No, cancelar!",
        confirmButtonClass: "btn btn-confirm mt-2",
        cancelButtonClass: "btn btn-cancel ml-2 mt-2"
      }).then(result => {
        if (result.value) {
          axios
            .post("/api/core/logs/system/remove", {
              date: fecha
            })
            .then(() => {
              self.$message("delete");
              self.get_data();
            })
            .catch(function(error) {
              console.log(error.response.data);
            });
        }
      });
    }
  },
  created() {
    this.get_data();
  },
  mounted: function() {
    this.$check_view_perms(this.user.all_permissions, "logs_system");
  }
};
</script>