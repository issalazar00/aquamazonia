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
              <div class="form-group col-3" v-show="f_estado == 1">
                <label for="siembra_activa">Siembras Activas</label>
                <select name="siembra_activa" class="custom-select" id="siembra_activa" v-model="f_siembra">
                  <option value="-1">--Seleccionar--</option>
                  <option v-for="(siembraActiva, index) in siembrasActivas" :key="index" :value="siembraActiva.id">
                    {{ siembraActiva.nombre_siembra }}</option>
                </select>
              </div>
              <div class="form-group col-3" v-show="f_estado == 0">
                <label for="siembra_inactiva">Siembras Inactivas</label>
                <select name="siembra_inactiva" class="custom-select" id="siembra_inactiva" v-model="f_siembra">
                  <option value="-1">--Seleccionar--</option>
                  <option v-for="(siembraInactiva, index) in siembrasInactivas" :key="index"
                    :value="siembraInactiva.id">{{ siembraInactiva.nombre_siembra }}</option>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="lote">Lotes:</label>
                <select class="custom-select" id="lote" v-model="f_lote">
                  <option value="-1">Seleccionar</option>
                  <option :value="lote.lote" v-for="(lote, index) in listadoLotes" :key="index">{{ lote.lote }}</option>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="especie">Especies</label>
                <select class="form-control" id="especie" v-model="f_especie">
                  <option value="-1">Seleccionar</option>
                  <option :value="les.id" v-for="(les, index) in listadoEspecies" :key="index">{{ les.especie }}
                  </option>
                </select>
              </div>
              <div class="form-group col-md-2">
                <label for="peso desde">peso desde (gr): </label>
                <input type="number" step="any" class="form-control" id="f_peso_d" v-model="f_peso_d">
              </div>
              <div class="form-group col-md-2">
                <label for="peso hasta">peso hasta (gr): </label>
                <input type="number" step="any" class="form-control" id="f_peso_h" v-model="f_peso_h">
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
            <div class="">
              <table class="table table-bordered table-striped table-sm table-sticky">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th class="fixed-column">Siembra</th>
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
                    <th>Incremento de biomasa <br> <small>(Kg)</small></th>
                    <th>Ganancia de peso por día <br> <small>(g / día)</small></th>
                    <th>Densidad parcial <br> <small>(Animales/m<sup>2</sup>)</small></th>
                    <th>Carga parcial <br> <small>(Kg/m<sup>2</sup>)</small> </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(le, index) in listadoExistencias" :key="index">
                    <td v-text="index + 1"></td>
                    <td class="fixed-column">
                      {{ le.nombre_siembra }}
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
                      {{ le.incremento_biomasa | numeral('0.00') }}
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
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
        'Incremento de biomasa \n (Kg)': 'incremento_biomasa',
        'Gananacia de peso por día \n (g/día)': 'ganancia_peso_dia',
        'Densidad parcial (Animales/m<sup>2</sup>)': 'densidad_parcial',
        'Carga parcial (Kg/m<sup>2</sup>)': {
          field: 'carga_parcial',
          callback: (value) => {
            return numeral(value).format('0.00');
          }
        }
      },
      listadoExistencias: [],
      totalizadoEspeciesSiembras: [],
      listadoEspecies: [],
      listadoSiembras: [],
      imprimirRecursos: [],
      listadoLotes: [],
      siembrasActivas: [],
      siembrasInactivas: [],
      f_siembra: '-1',
      f_estado: '1',
      f_lote: '-1',
      f_especie: '-1',
      f_inicio_d: '-1',
      f_inicio_h: '-1',
      f_peso_d: 0,
      f_peso_h: 1000,
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
      const data = {
        'f_siembra': this.f_siembra == '-1' ? '-1' : this.f_siembra,
        'f_estado': this.f_estado == '-1' ? '-1' : this.f_estado,
        'f_lote': this.f_lote == '-1' ? '-1' : this.f_lote,
        'f_especie': this.f_especie == '-1' ? '-1' : this.f_especie,
        'f_inicio_d': this.f_inicio_d == '-1' ? '-1' : this.f_inicio_d,
        'f_inicio_h': this.f_inicio_h == '-1' ? '-1' : this.f_inicio_h,
        'f_peso_d': this.f_peso_d == '-1' ? '-1' : this.f_peso_d,
        'f_peso_h': this.f_peso_h == '-1' ? '-1' : this.f_peso_h
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
    listarSiembras(estado_siembra) {
      let me = this;
      axios.get('api/siembras?estado_siembra=' + estado_siembra)
        .then(function (response) {
          me.siembrasActivas = response.data.listado_siembras;
          me.siembrasInactivas = response.data.listado_siembras_inactivas;
        })
    },
    listarLotes() {
      let me = this;
      axios.get("api/siembras/listado-lotes")
        .then(function (response) {
          me.listadoLotes = response.data;
        })
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
