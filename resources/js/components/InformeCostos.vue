<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes de Costos</div>
          <!-- Informe de producción -->
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
                <label for="siembra_activa">Siembras</label>
                <select name="siembra_activa" class="custom-select" id="siembra_activa" v-model="f_siembra"
                  v-if="f_estado != '-1'">
                  <template v-for="(siembra,
                  index) in listadoSiembras">
                    <option v-if="siembra.estado == f_estado" :key="index" :value="siembra.id">
                      {{
                          siembra.nombre_siembra
                      }}
                    </option>
                  </template>
                </select>
                <select name="siembra_activa" class="custom-select" id="siembra_activa" v-model="f_siembra"
                  v-if="f_estado == '-1'">
                  <template>
                    <option v-for="(siembra,
                    index) in listadoSiembras" :key="index" :value="siembra.id">
                      {{
                          siembra.nombre_siembra
                      }}
                    </option>
                  </template>
                </select>
              </div>

              <div class="form-group col-md-2">
                <label for="Fecha desde">Fecha inicio desde: </label>
                <input type="date" class="form-control" id="f_inicio_d">
              </div>
              <div class="form-group col-md-2">
                <label for="fecha hasta">Fecha inicio hasta: </label>
                <input type="date" class="form-control" id="f_inicio_h">
              </div>
              <div class="form-group col-md-2">
                <button class="btn btn-primary" @click="listar()">
                  Filtrar resultados
                </button>
              </div>
              <div class="form-group col-md-2">
                <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                  name="informe-costos.xls" type="xls">
                  <i class="fa fa-fw fa-download"></i> Generar Excel
                </downloadexcel>
              </div>
            </div>
            <div class="table-container" id="table-container2">
              <table class="table-sticky table table-sm table-hover table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>Siembra</th>
                    <th>Costo Horas</th>
                    <th>Costo Recursos</th>
                    <th>Costo Alimentos</th>
                    <th>Costo total de siembra</th>
                    <th>Costo total de producción parcial</th>
                  </tr>
                </thead>
                <tbody>
                  <tr class="text-right" v-for="(le, index) in listadoExistencias" :key="index">
                    <td v-text="index + 1"></td>
                    <td class="text-right">
                      {{ le.nombre_siembra }}
                    </td>
                    <td>
                      {{ le.costo_minutos_hombre | numeral('$0,0.00') }}
                    </td>
                    <td>
                      {{ le.costo_total_recurso | numeral('$0,0.00') }}
                    </td>
                    <td>
                      {{ le.costo_total_alimento | numeral('$0,0.00') }}
                    </td>
                    <td>
                      {{ le.costo_total_siembra | numeral('$0,0.00') }}
                    </td>
                    <td>
                      {{ le.costo_produccion_parcial | numeral('$0,0.00') }}
                    </td>
                  </tr>
                  <tr class="font-weight-bold text-right">
                    <td></td>
                    <td></td>
                    <td>{{ totalizadoSiembras.costo_minutos_hombre | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.costo_total_recurso | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.costo_total_alimento | numeral('0.00') }}</td>
                    <td>{{ totalizadoSiembras.costo_total_siembra | numeral('0.00') }}</td>
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
        'Costo Horas': {
          field: 'costo_minutos_hombre',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Costo recursos': {
          field: 'costo_total_recurso',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Costo alimentos': {
          field: 'costo_total_alimento',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Costo total siembra': {
          field: 'costo_total_siembra',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Costo total de producción parcial': {
          field: 'costo_produccion_parcial',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
      },
      listadoExistencias: [],
      totalizadoSiembras: [],
      listadoEspecies: [],
      listadoSiembras: [],
      imprimirRecursos: [],
      f_siembra: '-1',
      f_inicio_d: '',
      f_inicio_h: '',
      f_estado: "1",

    }
  },
  components: {
    downloadexcel,
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
    }
  },
  mounted() {
    this.listarEspecies();
    this.listarSiembras();
    this.listar();
  }
}
</script>
