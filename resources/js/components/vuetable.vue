<template>
  <div class="vuetable">
    <div class="mb-2">
      <div class="row">
        <div class="col-md-4" v-if="buscar">
          <label class="text-uppercase">Buscar:</label>
          <b-form-input
            id="buscador"
            size="sm"
            v-model="search"
            type="text"
            placeholder="Buscar.."
            autocomplete="off"
            @keyup.enter.native="vueTable()"
          ></b-form-input>
        </div>
        <!--Custom filters-->
        <div class="col-md-2" v-for="(filtro, index) in newFilters" :key="index">
          <label class="text-uppercase">{{ filtro.name }}:</label>
          <select
            class="form-control form-control-sm"
            v-model="newFilters[index].value"
            v-if="filtro.type == 'select'"
            @change="vueTable()"
          >
            <option value="null" disabled>Seleccionar {{ filtro.name }}</option>
            <option value>Todos</option>
            <option
              :value="modulo.id"
              v-for="(modulo, index) in filtro.data"
              :key="index"
            >{{ modulo[filtro.nameSelect] }}</option>
          </select>
          <input
            :type="filtro.type"
            v-model="newFilters[index].value"
            :placeholder="filtro.name + '...'"
            class="form-control form-control-sm"
            @change="vueTable()"
            v-else
          >
        </div>
        <!--Fin Custom filters-->
        <div class="col-md-1" v-if="buscar">
          <label class="text-white">Btn</label>
          <b-button class="btn-block" size="sm" variant="success" @click="vueTable()">
            <i class="fa fa-search"></i>
          </b-button>
        </div>
      </div>
    </div>
    <loader v-if="loading"></loader>
    <div class="dtable" v-else>
      <b-table
        :fields="labels"
        :foot-clone="footClone"
        small="small"
        :items="items.data"
        :bordered="true"
        show-empty
        empty-text="No hay datos disponibles"
      >
        <template v-for="(field, index) in customFields" :slot="field.key" slot-scope="data">
          <div class="custom-data" :key="index" v-if="field.html">
            <div class="custom-item" v-html="viewData(data.item, field)"></div>
          </div>
          <div class="custom-data" :key="index" v-else>
            <div class="custom-item">{{ viewData(data.item, field) }}</div>
          </div>
        </template>
        <template slot="btnDetalle" slot-scope="data" v-if="btnDetalle">
          <a
            href="javascript:void(0);"
            class="btn btn-secondary btn-icon btn-sm"
            @click.stop="data.toggleDetails"
          >
            <i class="fa fa-bars" v-if="!data.detailsShowing"></i>
            <i class="fa fa-times" v-else></i>
          </a>
        </template>
        <template slot="row-details" slot-scope="data">
          <div class="detail-table" v-html="viewDetalle(data.item)"></div>
        </template>
        <template slot="acciones" slot-scope="data">
          <span v-if="customButtons.length>=1">
            <a
              class="btn btn-lime btn-sm btn-icon"
              href="javascript:void(0);"
              v-b-tooltip.hover
              :title="btn.text"
              v-for="(btn, index) in customButtons"
              :key="index"
              :style="{ 'background-color': btn.color, 'border-color': btn.color}"
              @click="btn.link(data.item)"
            >
              <i :class="btn.icon"></i>
              <span v-if="btn.label">{{ btn.label }}</span>
            </a>
          </span>
          <a
            class="btn btn-primary btn-icon btn-sm"
            href="javascript:void(0);"
            v-if="showEditar"
            @click="btnEditar(data.item)"
            v-b-tooltip.hover
            title="Editar"
          >
            <i class="fa fa-edit"></i>
          </a>
          <a
            class="btn btn-danger btn-icon btn-sm"
            href="javascript:void(0);"
            v-if="btnElminar!=false"
            @click="borrar(data.item.id)"
            v-b-tooltip.hover
            title="Eliminar"
          >
            <i class="fa fa-times"></i>
          </a>
        </template>
      </b-table>
      <div class="pagination-table" v-if="pagination">
        <div class="row">
          <div class="col-md-6 col-12">
            <div class="text-left">
              <span class="total">Total de registros: {{ items.total }}</span>
            </div>
          </div>
          <div class="col-md-6 col-12">
            <div class="text-right">
              <b-pagination
                size="sm"
                :total-rows="items.total"
                v-model="currentPage"
                :per-page="items.per_page"
              ></b-pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { mapGetters } from "vuex";
import loader from "~/components/loader";
import swal from "sweetalert2";
export default {
  components: {
    loader
  },
  props: {
    fields: {
      type: Array,
      default: () => []
    },
    buscar: {
      type: Boolean,
      default: false
    },
    buscarMayus: {
      type: Boolean,
      default: true
    },
    pagination: {
      type: Boolean,
      default: true
    },
    footClone: {
      type: Boolean,
      default: false
    },
    url: {
      type: String,
      default: null
    },
    urlDelete: {
      type: String,
      default: null
    },
    otherData: {
      type: String,
      default: null
    },
    btnElminar: {
      type: Boolean,
      default: false
    },
    permiso_eliminar: {
      type: String,
      default: null
    },
    btnEnabled: {
      type: Boolean,
      default: true
    },
    btnEditar: {
      type: Function,
      default: () => false
    },
    showEditar: {
      type: Boolean,
      default: () => true
    },
    btnDetalle: {
      type: Boolean,
      default: false
    },
    detalle: {
      type: Array,
      default: () => []
    },
    customFields: {
      type: Array,
      default: () => []
    },
    customButtons: {
      type: Array,
      default: () => []
    },
    customFilter: {
      type: Array,
      default: () => []
    },
    value: {
      default: () => []
    }
  },
  data() {
    return {
      items: {},
      currentPage: 1,
      loading: null,
      search: "",
      labels: [],
      newFilters: []
    };
  },
  watch: {
    search: function(texto) {
      if (this.buscarMayus) {
        this.search = texto.toUpperCase();
      }
    },
    currentPage: function(val) {
      this.vueTable();
    },
    newFilters: {
      handler: function(val, oldVal) {
        //this.vueTable();
      },
      deep: true
    }
  },
  computed: mapGetters({
    user: "auth/user"
  }),
  methods: {
    vueTable: function() {
      let self = this;
      if (self.fields.length >= 1 && self.url != null) {
        let getPag = null;
        let getBuscar = "";
        /* comprobando si está activa la paginación */
        if (self.pagination) {
          let pagina = self.currentPage;
          if (self.currentPage >= 1) {
            getPag = "?page=" + pagina;
          }
        } else {
          getPag = "";
        }
        let character = self.pagination ? "&" : "?";
        /* comprobando si esta activo la opción de buscar */
        if (self.buscar) {
          getBuscar = character + "texto=" + self.search;
          character = "&";
        }
        /**
         * Aplicando filtros customizables
         */
        if (this.newFilters) {
          this.newFilters.forEach(filtro => {
            getBuscar =
              getBuscar +
              character +
              filtro.slug +
              "=" +
              encodeURI(filtro.value);
          });
        }
        self.loading = true;
        axios
          .get(self.url + getPag + getBuscar)
          .then(function(response) {
            self.loading = false;
            if (self.otherData != null) {
              let index = self.otherData;
              self.items.data = response.data[index];
            } else {
              self.items = response.data;
            }
            self.$emit("input", response.data);
          })
          .catch(function(error) {
            self.loading = true;
            console.log(error.response.data);
          });
      }
    },
    borrar: function(id) {
      if (this.$permission(this.user.all_permissions, this.permiso_eliminar)) {
        var self = this;
        let urlRemove = self.url;
        if (self.urlDelete != null) {
          urlRemove = self.urlDelete;
        }
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
            axios.delete(urlRemove + "/" + id).then(response => {
              self.$message("delete");
              self.vueTable();
            });
          }
        });
      } else {
        this.$opps_error();
      }
    },
    addActions: function(arr) {
      var action = [];
      if (this.btnEnabled == true) {
        action = [{ key: "acciones", label: "", class: "actions text-center" }];
      }
      this.labels = this.fields;
      this.labels = arr.concat(action);
    },
    viewDetalle: function(data) {
      if (this.detalle.length >= 1) {
        var arr = [];
        var html = "<ul>";
        this.detalle.forEach(item => {
          var id = item.id;
          var key_arr = item.nameRow;
          arr.push(data[id]);
          if (data[id] != null) {
            var resultado = "";
            /**
             * Se comprueba si el id buscado es un array
             */
            if (data[id] instanceof Array) {
              data[id].forEach(i => {
                var item_name = null;
                if (key_arr == "index") {
                  item_name = i;
                } else {
                  item_name = i[key_arr];
                }
                resultado +=
                  "<span class='badge badge-success text-uppercase mr-1'>" +
                  item_name +
                  "</span>";
              });
            } else if (data[id] instanceof Object) {
              let obj = data[id];
              let objKeys = Object.keys(obj);
              for (let index = 0; index < objKeys.length; index++) {
                const item = objKeys[index];
                resultado +=
                  "<span class='badge badge-success text-uppercase mr-1'>" +
                  item +
                  ": " +
                  obj[item] +
                  "</span>";
              }
            } else {
              resultado = data[id];
            }
            /**
             * Se comprueba si el resultado es nulo y no muestra esa fila
             */
            if (resultado != "") {
              html =
                html + "<li><b>" + item.title + ":</b> " + resultado + "</li>";
            }
          }
        });
        html = html + "</ul>";
        var filteredArr = arr.filter(function(item, index) {
          if (arr.indexOf(item) == index) return item;
        });
        if (filteredArr.length <= 0 && filteredArr[0] == null) {
          html = "No hay información disponible.";
        }
        return html;
      }
    },
    viewData: function(data, field) {
      let tempVariable = `data.${field.key}`; /** generando variable */
      let value = eval(tempVariable);
      let html = field.replace;
      if (value instanceof Array) {
        let nameObj = field.keyName;
        let newHtml = "";
        value.forEach(item => {
          newHtml +=
            "<span class='badge badge-success text-uppercase mr-1'>" +
            item[nameObj] +
            "</span>";
        });
        if (newHtml == "") {
          newHtml =
            "<span class='badge badge-success text-uppercase mr-1'>No hay registro</span>";
        }
        return this.replace_data(html, newHtml);
      } else {
        if (value == null || value == "") {
          value = "NO HAY DATO";
        }
        return this.replace_data(html, value);
      }
    },
    replace_data: function(html, data) {
      if (this.$isFunction(html)) {
        return html(data);
      } else {
        return html.replace(":data", data);
      }
    },
    /**
     * Buscar valor del select seleccionado
     */
    searchFilterOptions: function(name) {
      let dato = "";
      if (this.newFilters) {
        this.newFilters.forEach(item => {
          if (item.slug == name) {
            item.data.forEach(data => {
              if (data.id == item.value) {
                dato = data;
              }
            });
          }
        });
      }
      console.log(dato);
      return dato;
    }
  },
  created() {
    this.newFilters = this.customFilter;
  },
  mounted() {
    this.vueTable();
    if (this.btnDetalle) {
      var detalleField = [
        { key: "btnDetalle", label: "", class: "text-center" }
      ];
      this.labels = detalleField.concat(this.fields);
    } else {
      this.labels = this.fields;
    }
    this.addActions(this.labels);
  }
};
</script>