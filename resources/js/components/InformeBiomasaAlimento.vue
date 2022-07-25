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
                <label for="siembra">Siembras</label>
                <select class="form-control" id="siembra" v-model="f_siembra">
                  <option value="-1">Seleccionar</option>
                  <option :value="ls.id" v-for="(ls, index) in listadoSiembras" :key="index">{{ ls.nombre_siembra }}
                  </option>
                </select>
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
                    <th>Area m<sup>3</sup> </th>
                    <th>Cant Inicial (Animales) </th>
                    <th>Biomasa Inicial (Kg)</th>
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
                    <td>
                      {{ le.biomasa_disponible | numeral('0.00') }}
                    </td>
                    <td v-if="le.salida_biomasa">{{ le.salida_biomasa | numeral('0.00') }} kg</td>
                    <td v-else>0</td>
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
        'Biomasa disponible muestreo \n (Kg)': 'biomasa_disponible',
        'Biomasa cosechada \n (Kg)': 'salida_biomasa',
        'Mortalidad': 'mortalidad',
        'Mort. Kg': 'mortalidad_kg',
        'Total alimento (Kg)': 'cantidad_total_alimento',
        'Incremento de biomasa por alimento  \n (Kg)': 'incr_bio_acum_conver',
        'Conversion alimenticia': 'conversion_alimenticia',
        'Biomasa disponible por alimento \n (Kg)': 'bio_dispo_alimen',
        '% Supervievencia final': 'porc_supervivencia_final'
      },
      listadoExistencias: [],
      listadoEspecies: [],
      listadoSiembras: [],
      imprimirRecursos: [],
      f_siembra: '',
      f_especie: '',
      f_inicio_d: '',
      f_inicio_h: '',

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
        'f_inicio_d': this.f_inicio_d == '' ? '-1' : this.f_siembra,
        'f_inicio_h': this.f_inicio_h == '' ? '-1' : this.f_siembra,
      }

      axios.get("api/informes-biomasa-alimento", { params: data })
        .then(function (response) {
          me.listadoExistencias = response.data.existencias;
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
      axios.get("api/siembras")
        .then(function (response) {
          me.listadoSiembras = response.data.listado_siembras;
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
