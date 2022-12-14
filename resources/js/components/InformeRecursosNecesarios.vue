<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes recursos necesarios</div>
          <div class="card-body">
            <div class="col-md-12">
              <form class="row">
                <div class="form-group col-md-2">
                  <label for="estado"> Estado: </label>
                  <select class="custom-select" name="estado" id="estado" v-model="f_estado">
                    <option value="-1" disabled>--Seleccionar--</option>
                    <option value="0">Inactiva</option>
                    <option value="1">Activa</option>
                  </select>
                </div>
                <div class="form-group col-3">
                  <label for="siembra_activa">Siembras
                    {{ f_estado == 0 ? "inactivas" : "activas" }} : </label>
                  <template v-if="f_estado != '-1'">
                    <v-select :options="filteredItems" label="nombre_siembra" :reduce="(siembra) => siembra.id"
                      v-model="f_siembra" />
                  </template>
                  <template v-if="f_estado == '-1'">
                    <v-select :options="listadoSiembras" label="nombre_siembra" :reduce="(siembra) => siembra.id"
                      v-model="f_siembra" />
                  </template>
                </div>
                <div class="form-group col-md-2">
                  <label for="contenedor">Estanque:</label>
                  <select class="custom-select" id="contenedor" v-model="f_contenedor">
                    <option value="-1">Seleccionar</option>
                    <option :value="cont.id" v-for="(cont, index) in listadoEstanques" :key="index">
                      {{ cont.contenedor }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <label for="f_actividad">Tipo de Actividad: </label>
                  <select class="custom-select" id="f_actividad" v-model="f_actividad" @click="cambiarActividad()">
                    <option value="-1" selected>Seleccionar</option>
                    <option v-for="(actividad, index) in listadoActividades" :key="index" v-bind:value="actividad.id">
                      {{ actividad.actividad }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-2">
                  <button class="btn btn-primary rounded-circle mt-4" type="button" @click="listar()">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
                <div class="col-md-2">
                  <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                    name="informe-consolidado-recursos-necesarios.xls" type="xls">
                    <i class="fa fa-fw fa-download"></i> Generar Excel
                  </downloadexcel>
                </div>
              </form>
            </div>
            <div class="table-container" id="table-container2">
              <table class="table-sticky table table-sm table-hover table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>Siembra</th>
                    <th>Estado</th>
                    <th>Tipo actividad</th>
                    <th>Minutos hombre</th>
                    <th>Costo minutos hombre</th>
                    <th v-if="tipoActividad != 'Alimentación'">
                      Cantidad Recurso
                    </th>
                    <th v-if="tipoActividad != 'Alimentación'">
                      Costo Recurso
                    </th>
                    <th v-if="tipoActividad == 'Alimentación'">
                      Cantidad Alimento
                    </th>
                    <th v-if="tipoActividad == 'Alimentación'">
                      Costo Alimento
                    </th>
                    <th>Costo total actividad</th>
                    <th>% Costo total de producción</th>
                  </tr>
                </thead>
                <tbody v-if="!loading">
                  <tr v-for="(lrn, index) in listado" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="lrn.nombre_siembra"></td>
                    <td v-if="lrn.estado == 1">Activa</td>
                    <td v-else>Inactiva</td>
                    <td v-text="lrn.actividad"></td>
                    <td v-text="lrn.minutos_hombre + ' min'"></td>
                    <td class="text-right">{{ lrn.costo_minutos | numeral('$0,0.00') }}</td>
                    <td class="text-right" v-if="tipoActividad != 'Alimentación'">{{ lrn.cantidad_recurso |
                        numeral('0.00')
                    }}</td>
                    <td class="text-right" v-if="tipoActividad != 'Alimentación'">{{ lrn.costo_recurso |
                        numeral('$0,0.00')
                    }}</td>
                    <td class="text-right" v-if="tipoActividad == 'Alimentación'">{{ lrn.cantidad_alimento |
                        numeral('0.00')
                    }}</td>
                    <td class="text-right" v-if="tipoActividad == 'Alimentación'">{{ lrn.costo_alimento |
                        numeral('$0,0.00')
                    }}</td>
                    <td class="text-right">{{ lrn.costo_total_actividad | numeral('$0,0.00') }}</td>
                    <td class="text-right">
                      <!-- {{ lrn.por_total_produccion = ((lrn.costo_total_actividad * 100)/(lrn.costoTotalSiembra)) }} -->
                      <span v-if="lrn.porcentaje_total_produccion">
                        {{ lrn.porcentaje_total_produccion | numeral('0.00') }} %
                      </span>
                    </td>
                  </tr>
                </tbody>
                <tbody v-else>
                  Cargando ...
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Form, HasError, AlertError } from "vform";
import downloadexcel from "vue-json-excel";
export default {
  data() {
    return {
      json_fields: {
        Siembra: "nombre_siembra",
        Estado: "estado",
        "Tipo actividad": "actividad",
        "Minutos hombre": {
          field: "minutos_hombre",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Costo total minutos": {
          field: "costo_minutos",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Cantidad total recurso": {
          field: "cantidad_recurso",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Costo total recurso": {
          field: "costo_recurso",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Cantidad total alimento": {
          field: "cantidad_alimento",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Costo total alimento": {
          field: "costo_alimento",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "Costo total actividad": {
          field: "costo_total_actividad",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        "% Costo total producción": {
          field: "porcentaje_total_produccion",
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
      },
      tipoActividad: "",
      f_actividad: "",
      f_siembra: "",
      f_contenedor: "",
      f_estado: "",
      listado: [],
      listadoSiembras: [],
      listadoActividades: [],
      listadoEstanques: [],
      loading: 0
    };
  },
  components: {
    downloadexcel,
  },

  computed: {
    filteredItems() {
      return this.listadoSiembras.filter((item) => item.estado == this.f_estado);
    }
  },
  methods: {
    async fetchData() {
      let me = this;
      const response = await this.listado;
      return this.listado;
    },

    cambiarActividad() {
      if (this.f_actividad == 1) {
        this.tipoActividad = "Alimentación";
      } else this.tipoActividad = "";
    },

    listar() {
      let me = this;
      me.loading = 1;
      if (this.f_siembra == "") {
        this.f_s = "-1";
      } else {
        this.f_s = this.f_siembra;
      }
      if (this.f_estado == "") {
        this.f_e = "-1";
      } else {
        this.f_e = this.f_estado;
      }
      if (this.f_contenedor == "") {
        this.cont = "-1";
      } else {
        this.cont = this.f_contenedor;
      }
      if (this.f_actividad == "") {
        this.actividad = "-1";
      } else {
        this.actividad = this.f_actividad;
      }

      const data = {
        f_siembra: this.f_s,
        f_estado: this.f_e,
        f_actividad: this.actividad,
        f_contenedor: this.cont,
      };
      axios.get("api/informes-recursos-necesarios", { params: data }).then((response) => {
        me.listado = response.data.recursosNecesarios.data;
        // me.promedios = response.data.promedioRecursos;
        me.loading = 0;
      });
    },

    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
      });
    },

    listarActividades() {
      let me = this;
      axios.get("api/actividades").then(function (response) {
        me.listadoActividades = response.data;
      });
    },

    listarEstanques() {
      let me = this;
      axios.get("api/contenedores").then(function (response) {
        me.listadoEstanques = response.data;
      });
    },

  },
  mounted() {
    this.listar();
    this.listarSiembras();
    this.listarActividades();
    this.listarEstanques();
  },
};
</script>
