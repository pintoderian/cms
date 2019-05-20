<template>
    <div class="system-view-log">
        <!-- begin breadcrumb -->
        <ol class="breadcrumb pull-right">
            <li class="breadcrumb-item">
                <router-link :to="{name:'dashboard'}">Inicio</router-link>
            </li>
            <li class="breadcrumb-item">
                <a href="javascript:">Core</a>
            </li>
            <li class="breadcrumb-item">Logs</li>
            <li class="breadcrumb-item active">Logs del sistema</li>
        </ol>
        <!-- end breadcrumb -->
        <!-- begin page-header -->
        <h1 class="page-header">Logs del sistema / {{ fecha }}</h1>
        <!-- end page-header -->
        <panel noButton="true">
            <template slot="header">
                <h4 class="panel-title">Logs / {{ fecha }}</h4>
            </template>
            <div class="row">
                <div class="col-lg-2">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa fa-fw fa-flag"></i> Niveles
                        </div>
                        <div class="list-group list-group-flush log-menu">
                            <div :key="index" v-for="(log, index) in datos.logs">
                                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center disabled"
                                   v-if="log.count == 0"
                                >
                                    <span class="level-name">
                                      <span v-html="log.icon"></span>
                                      <small>{{ log.name }}</small>
                                    </span>
                                    <span class="badge empty">{{ log.count }}</span>
                                </a>
                                <router-link
                                    :class="'list-group-item list-group-item-action d-flex justify-content-between align-items-center level-'+index+' '+(datos.level == index ? 'active': '')"
                                    :to="{ name: 'seguridad.verlogs',  params:{ fecha:fecha, tipo:index } }"
                                    v-else
                                >
                                    <span class="level-name">
                                      <span v-html="log.icon"></span>
                                      <small>{{ log.name }}</small>
                                    </span>
                                    <span :class="'badge badge-level-'+index">{{ log.count }}</span>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-10">
                    <div class="card mb-4">
                        <div class="card-header">Información del log:
                            <div class="group-btns pull-right">
                                <button @click="Descargar()" class="btn btn-sm btn-success waves-effect waves-light">
                                    <i class="fa fa-download"></i> DESCARGAR
                                </button>
                                <button @click="Eliminar()" class="btn btn-sm btn-danger waves-effect waves-light">
                                    <i class="fa fa-trash-o"></i> BORRAR
                                </button>
                            </div>
                        </div>
                        <div class="table-responsive" v-if="datos.info">
                            <table class="table table-condensed mb-0">
                                <tbody>
                                <tr>
                                    <td>File path :</td>
                                    <td colspan="7">{{ datos.info.path }}</td>
                                </tr>
                                <tr>
                                    <td>Total entradas:</td>
                                    <td>
                                        <span class="badge badge-primary">{{ datos.info.total }}</span>
                                    </td>
                                    <td>Tamaño :</td>
                                    <td>
                                        <span class="badge badge-primary">{{ datos.info.size }}</span>
                                    </td>
                                    <td>Fecha de creación:</td>
                                    <td>
                                        <span class="badge badge-primary">{{ datos.info.createdAt }}</span>
                                    </td>
                                    <td>Fecha de actualización:</td>
                                    <td>
                                        <span class="badge badge-primary">{{ datos.info.updatedAt }}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div v-html="datos.header"></div>
                        <div class="table-responsive" v-if="datos.entries">
                            <table class="table mb-0" id="entries">
                                <thead>
                                <tr>
                                    <th style="width: 120px;">Nivel</th>
                                    <th style="width: 65px;">Hora</th>
                                    <th>Cabecera</th>
                                    <th class="text-right">Acción</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr :key="key" v-for="(dato, key) in datos.entries.data">
                                    <td>
                                <span
                                        :class="'badge badge-level-'+dato.level"
                                    >{{ nivel(dato.level, datos.levels) }}</span>
                                    </td>
                                    <td>{{ dato.datetime.toString().slice(10,16) }}</td>
                                    <td>
                                        <small>{{ dato.header }}</small>
                                    </td>
                                    <td>
                                        <button
                                            @click="Ver(dato.stack)"
                                            class="btn btn-secondary btn-sm btn-sm waves-effect waves-light"
                                            type="button"
                                        >
                                            <i class="fa fa-eye"></i> VER
                                        </button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <b-pagination :per-page="perPage" :total-rows="total" size="sm"
                                      v-model="currentPage"></b-pagination>
                    </div>
                </div>
            </div>
        </panel>
        <b-modal hide-footer id="verError" ref="verError" size="lg" title="Error Log">
            <div class="error-log">{{ errorLog }}</div>
        </b-modal>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import swal from "sweetalert2";

    export default {
        data() {
            return {
                fecha: this.$route.params.date,
                tipo: this.$route.params.type,
                datos: {},
                currentPage: 1,
                total: 0,
                loading: true,
                perPage: 0,
                errorLog: "",
            }
        },
        computed: mapGetters({
            user: "auth/user"
        }),
        watch: {
            currentPage: function (val) {
                this.get_data();
            },
            $route(to, from) {
                if (from.params.tipo !== to.params.tipo) {
                    this.tipo = this.$route.params.tipo;
                    this.get_data();
                }
            }
        },
        methods: {
            get_data: function () {
                let self = this;
                self.loading = true;
                axios
                    .get(
                        "/api/core/logs/system/" +
                        self.fecha +
                        "/" +
                        self.tipo +
                        "?page=" +
                        self.currentPage
                    )
                    .then(function (response) {
                        self.datos = response.data;
                        self.total = self.datos.entries.total;
                        self.perPage = self.datos.entries.per_page;
                        self.loading = false;
                    })
                    .catch(function (error) {
                        self.$router.push({name: "logs.system"});
                    });
            },
            nivel: function (level, array) {
                let nombre;
                let arr = array;
                if (arr.hasOwnProperty(level)) {
                    nombre = arr[level];
                }
                return nombre;
            },
            Ver: function (error) {
                this.errorLog = error;
                this.$refs.verError.show();
            },
            Descargar: function () {
                window.open("/api/core/logs/system/" + this.fecha + "/download", "_blank");
            },
            Eliminar: function () {
                let self = this;
                let fecha = self.fecha;
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
                            .then(function (response) {
                                self.$router.push({name: "logs.system"});
                            })
                            .catch(function (error) {
                                console.log(error.response.data);
                            });
                    }
                });
            }
        },
        created() {
            setTimeout(() => {
                this.get_data();
            }, 200);
        },
        mounted: function () {
            this.$check_view_perms(this.user.all_permissions, "logs_system_view");
        }
    }
</script>