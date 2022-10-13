<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes biomasa por alimento</div>
          <!-- <a href="informe-excel"><button type="submit" class="btn btn-success" name="infoSiembras"><i class="fa fa-fw fa-download"></i> Generar Excel </button></a> -->
          <div class="card-body">
            <div class="row text-left">
              <h5>Filtrar por: </h5>
            </div>
            <div class="row">
              <div class="form-group col-md-2">
                <label for="f_estado">
                  Estado:
                  <select class="custom-select" name="estado" id="estado" v-model="f_estado">
                    <option value="-1">--Seleccionar--</option>
                    <option value="0">Inactiva</option>
                    <option value="1">Activa</option>
                  </select>
                </label>
              </div>
              <div class="form-group col-3">
                <label for="siembra_activa">Siembras
                  {{ f_estado == 0 ? "inactivas" : "activas" }} :
                </label>
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
                <label for="Fecha desde">Fecha inicio desde: </label>
                <input type="date" class="form-control" id="f_inicio_d" v-model="f_inicio_d">
              </div>
              <div class="form-group col-md-2">
                <label for="fecha hasta">Fecha inicio hasta: </label>
                <input type="date" class="form-control" id="f_inicio_h" v-model="f_inicio_h">
              </div>
              <div class="form-group col-md-2">
                <button class="btn btn-primary" @click="listar()">
                  Filtrar resultados
                </button>
              </div>
              <div class="form-group col-md-2">
                <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                  name="informe-ciclo-productivo.xls" type="xls">
                  <i class="fa fa-fw fa-download"></i> Generar Excel
                </downloadexcel>
              </div>
            </div>
            <div class="table-container" id="table-container2">
              <table class="table-sticky table table-sm table-hover table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th class="fixed-column">Siembra</th>
                    <th>Area (m²)<sup>3</sup> </th>
                    <th>Cant Inicial (Animales) </th>
                    <th>Biomasa Inicial (Kg)</th>
                    <th>Cantidad actual</th>
                    <th>Biomasa disponible muestreo (Kg)</th>
                    <th>Biomasa cosechada (Kg)</th>
                    <th>Mortalidad</th>
                    <th>Mort. Kg</th>
                    <th>Total alimento (Kg)</th>
                    <th>Incremento de biomasa por alimento (Kg)</th>
                    <th>Conversión Alimenticia</th>
                    <th>Biomasa disponible por alimento (Kg)</th>
                    <th>% Supervivencia final</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(le, index) in listadoExistencias" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="le.nombre_siembra" class="fixed-column"></td>
                    <td v-text="le.capacidad"></td>
                    <td v-text="le.cantidad_inicial"></td>
                    <td>
                      {{ le.biomasa_inicial | numeral('0.00') }}
                    </td>
                    <td> {{le.cantidad_actual | numeral('0.00') }}</td>
                    <td>
                      {{ le.biomasa_disponible | numeral('0.00') }}
                    </td>
                    <td>{{ le.salida_biomasa | numeral('0.00') }} kg</td>
                    <td>
                      {{ le.mortalidad | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.mortalidad_kg | numeral('0.00') }} kg
                    </td>
                    <td>
                      {{ le.cantidad_total_alimento | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.incr_bio_acum_conver | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.conversion_alimenticia | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.bio_dispo_alimen | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.porc_supervivencia_final | numeral('0.00') }}
                    </td>
                  </tr>
                  <tr class="font-weight-bold">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ totalizadoSiembras.biomasa_inicial | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.biomasa_disponible | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.salida_biomasa | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.mortalidad | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.mortalidad_kg | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.cantidad_total_alimento | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.incr_bio_acum_conver | numeral('0.00') }}</td>
                    <td></td>
                    <td>{{ totalizadoSiembras.bio_dispo_alimen | numeral('0.00') }}</td>
                    <td></td>
                  </tr>
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
import downloadexcel from "vue-json-excel";
export default {
  data() {
    return {
      json_fields: {
        'Siembra': 'nombre_siembra',
        'Area / (m<sup>2</sup>)': 'capacidad',
        'Cantidad inicial \n (Animales)': 'cantidad_inicial',
        'Biomasa inicial \n (Kg)': {
          field: 'biomasa_inicial',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Biomasa disponible muestreo \n (Kg)': {
          field: 'biomasa_disponible',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Biomasa cosechada \n (Kg)': {
          field: 'salida_biomasa',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Mortalidad': {
          field: 'mortalidad',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Mort. Kg': {
          field: 'mortalidad_kg',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Total alimento (Kg)': {
          field: 'cantidad_total_alimento',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Incremento de biomasa por alimento  \n (Kg)': {
          field: 'incr_bio_acum_conver',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Conversion alimenticia': {
          field: 'conversion_alimenticia',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Biomasa disponible por alimento \n (Kg)': {
          field: 'bio_dispo_alimen',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        '% Supervievencia final': {
          field: 'porc_supervivencia_final',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        }
      },
      listadoExistencias: [],
      listadoEspecies: [],
      listadoSiembras: [],
      imprimirRecursos: [],
      totalizadoSiembras: [],
      f_siembra: '-1',
      f_inicio_d: '',
      f_inicio_h: '',
      f_estado: "1",

    }
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
      return this.listadoExistencias;
    },
    listar() {
      let me = this;

      const data = {
        'f_siembra': this.f_siembra == '' ? '-1' : this.f_siembra,
        'f_estado': this.f_estado == '-1' ? '-1' : this.f_estado,
        'f_inicio_d': this.f_inicio_d == '' ? '-1' : this.f_siembra,
        'f_inicio_h': this.f_inicio_h == '' ? '-1' : this.f_siembra,
      }

      axios.get("api/informes-biomasa-alimento", { params: data })
        .then(function (response) {
          me.listadoExistencias = response.data.existencias;
          me.totalizadoSiembras = response.data.totalizadoSiembras;
        })
    },
    listarEspecies() {
      let me = this;
      axios.get("api/especies")
        .then(function (response) {
          me.listadoEspecies = response.data
        })
    },
    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado")
        .then(function (response) {
          me.listadoSiembras = response.data;
        })
    },

  },
  mounted() {
    this.listar();
    this.listarEspecies();
    this.listarSiembras();

  }
}
</script>
