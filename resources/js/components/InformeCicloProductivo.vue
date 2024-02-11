<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes ciclo productivo</div>
          <!-- <a href="informe-excel"><button type="submit" class="btn btn-success" name="infoSiembras"><i class="fa fa-fw fa-download"></i> Generar Excel </button></a> -->
          <div class="card-body">
            <div class="row text-left">
              <h5>Filtrar por: </h5>
            </div>
            <div class="row">
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="estado_siembta">Estado de siembra</label>
                <select name="f_estado" class="custom-select" id="f_estado" v-model="f_estado">
                  <option value="-1">Todas</option>
                  <option value="1">Activas</option>
                  <option value="0">Inactivas</option>
                </select>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
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
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="categoria" class="col-form-label">Fase
                </label>
                <v-select label="phase" class="w-100" v-model="search_phase" :reduce="(option) => option.id"
                  :filterable="false" :options="listPhases" @search="onSearchPhase">
                  <template slot="no-options">
                    Escribe para iniciar la búsqueda
                  </template>
                  <template slot="option" slot-scope="option">
                    <div class="d-center">
                      {{ option.phase }}
                    </div>
                  </template>
                  <template slot="selected-option" slot-scope="option">
                    <div class="selected d-center">
                      {{ option.phase }}
                    </div>
                  </template>
                </v-select>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="search_type">Tipo de siembra</label>
                <select name="search_type" class="custom-select w-100" id="search_type" v-model="search_type">
                  <option value="">Todas</option>
                  <option value="Monocultivo">Monocultivo</option>
                  <option value="Policultivo">Policultivo</option>
                </select>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="lote">Lotes:</label>
                <select class="custom-select" id="lote" v-model="f_lote">
                  <option value="-1">Seleccionar</option>
                  <option :value="lote.lote" v-for="(lote, index) in listadoLotes" :key="index">{{ lote.lote }}</option>
                </select>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="especie">Especies</label>
                <select class="form-control" id="especie" v-model="f_especie">
                  <option value="-1">Seleccionar</option>
                  <option :value="les.id" v-for="(les, index) in listadoEspecies" :key="index">{{ les.especie }}
                  </option>
                </select>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="peso desde">peso desde (gr): </label>
                <input type="number" step="any" class="form-control" id="f_peso_d" v-model="f_peso_d">
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="peso hasta">peso hasta (gr): </label>
                <input type="number" step="any" class="form-control" id="f_peso_h" v-model="f_peso_h">
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="Fecha desde">Fecha inicio desde: </label>
                <input type="date" class="form-control" id="f_inicio_d" v-model="f_inicio_d">
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="fecha hasta">Fecha inicio hasta: </label>
                <input type="date" class="form-control" id="f_inicio_h" v-model="f_inicio_h">
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <label for="search_nro_results">Mostrar {{ search_nro_results }} resultados por página</label>
                <input type="number" v-model="search_nro_results" class="form-control">
              </div>

              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <button class="btn btn-primary" @click="listar()">
                  Filtrar resultados
                </button>
              </div>
              <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                  name="informe-ciclo-productivo.xls" type="xls">
                  <i class="fa fa-fw fa-download"></i> Generar Excel
                </downloadexcel>
              </div>
            </div>
            <div class="">
              <table class="table table-bordered table-striped table-sm table-sticky">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th class="fixed-column">Siembra</th>
                    <th>Tipo</th>
                    <th>Fase</th>
                    <th>Lote</th>
                    <th>Area</th>
                    <th>Especie</th>
                    <th>Inicio siembra</th>
                    <th>Cant Inicial <br> <small>(Animales)</small></th>
                    <th>Peso Inicial <br> <small>(g)</small></th>
                    <th>Animales finales</th>
                    <th>Peso Actual <br> <small>(g)</small></th>
                    <th>Fecha último registro</th>
                    <th>Tiempo de cultivo <br> <small>(Días)</small></th>
                    <th>Tiempo de cultivo <br> <small>(Meses)</small></th>
                    <th>Biomasa muestreo <br> <small>(Kg)</small></th>
                    <th>Biomasa cosechada <br> <small>(Kg)</small></th>
                    <th>Mortalidad Animales</th>
                    <th>Mort. Kg</th>
                    <th>% Mortalidad</th>
                    <th>Animales cosechados</th>
                    <!-- <th>Incremento de biomasa <br> <small>(Kg)</small></th> -->
                    <th>Ganancia de peso por día <br> <small>(g / día)</small></th>
                    <th>Densidad parcial <br> <small>(Animales/m<sup>2</sup>)</small></th>
                    <th>Carga parcial <br> <small>(Kg/m<sup>2</sup>)</small> </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(le, index) in listadoExistencias.data" :key="index">
                    <td v-text="index + 1"></td>
                    <td class="fixed-column">
                      {{ le.nombre_siembra }}
                    </td>
                    <td>{{ le.siembra.tipo }}</td>
                    <td>
                      <span v-if="le.siembra.phase_id">
                        {{ le.siembra.phase.phase }}
                      </span>
                    </td>
                    <td>
                      {{ le.lote }}
                    </td>
                    <td>
                      {{ le.capacidad }}
                    </td>
                    <td>
                      {{ le.especie.especie }}
                    </td>
                    <td>
                      {{ le.fecha_inicio }}
                    </td>
                    <td>
                      {{ le.cantidad_inicial }}
                    </td>
                    <td>
                      {{ le.peso_inicial }} gr
                    </td>
                    <td>
                      {{ le.cantidad_actual | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.peso_actual }} gr
                    </td>
                    <td>
                      {{ le.fecha_registro }}
                    </td>
                    <td>{{ le.intervalo_tiempo }} días</td>
                    <td>{{ le.intervalo_tiempo_months | numeral('0.00') }} meses</td>
                    <td>
                      {{ le.biomasa_disponible | numeral('0.00') }} kg
                    </td>
                    <td>{{ le.salida_biomasa | numeral('0.00') }} kg</td>
                    <td>{{ le.mortalidad | numeral('0.00') }}</td>
                    <td> {{ le.mortalidad_kg | numeral('0.00') }} kg</td>
                    <td>{{ le.mortalidad_porcentaje | numeral('0.00') }}</td>
                    <td>{{ le.salida_animales_sin_mortalidad |
                      numeral('0.00')
                    }}</td>
                    <td>
                      <!-- {{ le.incremento_biomasa | numeral('0.00') }} -->
                    </td>
                    <td>
                      {{ le.ganancia_peso_dia | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.densidad_parcial | numeral('0.00') }}
                    </td>
                    <td>
                      {{ le.carga_parcial | numeral('0.00') }}
                    </td>
                  </tr>
                  <tr class="h6 font-weight-bold">
                    <td colspan="6"></td>

                    <td>{{ totalizadoEspeciesSiembras.cantidad_inicial | numeral('0,00') }}</td>
                    <td>{{ totalizadoEspeciesSiembras.peso_inicial | numeral('0,00') }}</td>
                    <td>{{ totalizadoEspeciesSiembras.cantidad_actual | numeral('0,00') }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                      {{ totalizadoEspeciesSiembras.biomasa_disponible | numeral('0.00') }}
                    </td>
                    <td>
                      {{ totalizadoEspeciesSiembras.salida_biomasa | numeral('0.00') }}
                    </td>
                    <td>{{ totalizadoEspeciesSiembras.mortalidad | numeral('0.00') }}</td>
                    <td>
                      {{ totalizadoEspeciesSiembras.mortalidad_kg | numeral('0.00') }}
                    </td>
                    <td></td>
                    <td>
                      {{ totalizadoEspeciesSiembras.salida_animales_sin_mortalidad | numeral('0.00') }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
              <pagination :align="'center'" :data="listadoExistencias" :limit="2" @pagination-change-page="listar">
                <span slot="prev-nav"><i class="fas fa-arrow-left"></i></span>
                <span slot="next-nav"><i class="fas fa-arrow-right"></i></span>
              </pagination>
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
        "Tipo": "siembra.tipo",
        "Fase": "siembra.phase.phase",
        'Lote': 'lote',
        'Area': 'capacidad',
        'Especie': 'especie.especie',
        'Inicio siembra': 'fecha_inicio',
        'Cantidad Inicial \n (Animales)': 'cantidad_inicial',
        'Peso inicial \n (g)': 'peso_inicial',
        'Animales finales':
        {
          field: 'cantidad_actual',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Peso actual \n (g)': {
          field: 'peso_actual',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Fecha último registro': 'fecha_registro',
        'Tiempo de cultivo \n (Días) ': 'intervalo_tiempo',
        'Tiempo de cultivo \n (Meses) ': {
          field: 'intervalo_tiempo_months',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Biomasa muestreo \n (Kg)': {
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
        'Mortalidad Animales': {
          field: 'mortalidad',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        'Mortalidad kg': 'mortalidad_kg',
        '% Mortalidad': 'mortalidad_porcentaje',
        'Animales cosechados': {
          field: 'salida_animales_sin_mortalidad',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        },
        // 'Incremento de biomasa \n (Kg)': 'incremento_biomasa',
        'Gananacia de peso por día \n (g/día)': 'ganancia_peso_dia',
        'Densidad parcial (Animales/m<sup>2</sup>)': 'densidad_parcial',
        'Carga parcial (Kg/m<sup>2</sup>)': {
          field: 'carga_parcial',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        }
      },
      listadoExistencias: {},
      totalizadoEspeciesSiembras: [],
      listadoEspecies: [],
      listadoSiembras: [],
      imprimirRecursos: [],
      listadoLotes: [],
      listPhases: [],
      f_siembra: '-1',
      f_estado: '1',
      f_lote: '-1',
      f_especie: '-1',
      f_inicio_d: '-1',
      f_inicio_h: '-1',
      f_peso_d: 0,
      f_peso_h: 1000,
      search_phase: "",
      search_type: "",
      search_nro_results: "15"
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
      return this.listadoExistencias.data;
    },

    listar(page = 1) {
      const data = {
        'f_siembra': this.f_siembra == '-1' ? '-1' : this.f_siembra,
        'f_estado': this.f_estado == '-1' ? '-1' : this.f_estado,
        'f_lote': this.f_lote == '-1' ? '-1' : this.f_lote,
        'f_especie': this.f_especie == '-1' ? '-1' : this.f_especie,
        'f_inicio_d': this.f_inicio_d == '-1' ? '-1' : this.f_inicio_d,
        'f_inicio_h': this.f_inicio_h == '-1' ? '-1' : this.f_inicio_h,
        'f_peso_d': this.f_peso_d == '-1' ? '-1' : this.f_peso_d,
        'f_peso_h': this.f_peso_h == '-1' ? '-1' : this.f_peso_h,
        phase: this.search_phase,
        type: this.search_type,
        nro_results: this.search_nro_results,
        page: page
      }
      let me = this

      axios.get("api/traer-existencias", { params: data })
        .then(function (response) {
          me.listadoExistencias = response.data.existencias;
          me.totalizadoEspeciesSiembras = response.data.totalizadoEspeciesSiembras;
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
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
      });
    },
    listarLotes() {
      let me = this;
      axios.get("api/siembras/listado-lotes")
        .then(function (response) {
          me.listadoLotes = response.data;
        })
    },
    onSearchPhase(search, loading) {
      if (search.length) {
        loading(true);
        let data = {
          phase: search
        };

        axios.get(`api/phases/get`, {
          params: data
        })
          .then((response) => {
            this.listPhases = (response.data.phases.data);
            loading(false)
          })
          .catch(e => console.log(e))
      }
    }
  },
  mounted() {
    this.listar();
    this.listarEspecies();
    this.listarSiembras();
    this.listarLotes();
  }
}
</script>
