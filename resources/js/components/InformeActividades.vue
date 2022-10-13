<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes Actividades (Muestreo y Pesca)</div>
          <!-- <a href="informe-excel"><button type="submit" class="btn btn-success" name="infoSiembras"><i class="fa fa-fw fa-download"></i> Generar Excel </button></a> -->
          <div class="card-body">
            <div class="row mb-1">
              <div class="col-md-12">
                <h2>Filtrar por:</h2>
                <form class="row" method="POST" action="informe-excel" target="_blank">
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
                    <label for="contenedor">Estanque:</label>
                    <select class="custom-select" id="contenedor" v-model="id_contenedor">
                      <option value="-1">Seleccionar</option>
                      <option :value="cont.id" v-for="(cont, index) in listadoEstanques" :key="index">
                        {{ cont.contenedor }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="lote">Lotes:</label>
                    <select class="custom-select" id="lote" v-model="f_lote">
                      <option value="-1">Seleccionar</option>
                      <option :value="lote.lote" v-for="(lote, index) in listadoLotes" :key="index">
                        {{ lote.lote }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="Especie">Especie</label>
                    <select class="form-control" id="f_especie" v-model="f_especie">
                      <option value="-1" selected>Seleccionar</option>
                      <option :value="especie.id" v-for="especie in listadoEspecies" :key="especie.id">
                        {{ especie.especie }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="actividad">Tipo actividad: </label>
                    <select class="form-control" id="actividad" v-model="f_actividad" name="tipo_actividad">
                      <option selected value="">Seleccionar</option>
                      <option value="0">Muestreo</option>
                      <option value="1">Pesca</option>
                      <option value="2">Mortalidad Inicial</option>
                      <option value="3">Peso Inicial</option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="peso desde">peso desde (gr): </label>
                    <input type="number" step="any" class="form-control" id="f_peso_d" v-model="f_peso_d" />
                  </div>
                  <div class="form-group col-md-2">
                    <label for="peso hasta">peso hasta (gr): </label>
                    <input type="number" step="any" class="form-control" id="f_peso_h" v-model="f_peso_h" />
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Desde: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="f_fecha_d"
                      v-model="f_fecha_d" />
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Hasta: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="f_fecha_h"
                      v-model="f_fecha_h" />
                  </div>
                  <div class="form-group col-md-1">
                    <label for="">Buscar</label>
                    <button class="btn btn-primary form-control" type="button" @click="listarRegistros()">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                  <div class="form-group col-md-2">
                    <downloadexcel class="btn btn-success" :fetch="fetchData" :fields="json_fields"
                      name="informe-muestreos.xls" type="xls">
                      <i class="fa fa-fw fa-download"></i> Generar Excel
                    </downloadexcel>
                  </div>
                </form>
              </div>
            </div>
            <div class="table-container" id="table-container2">
              <table class="table-sticky table table-sm table-hover table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>Siembra</th>
                    <th>Lote</th>
                    <th>Fecha de registro</th>
                    <th>Especie</th>
                    <th>Tipo <br />actividad</th>
                    <th>Peso actual (g) </th>
                    <th>KG cosecha</th>
                    <th>Biomasa muestreo (kg)</th>
                    <th>Animales Actuales</th>
                    <th>Biomasa disponible por alimento</th>
                    <th>Animales Cosechados</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(lr, index) in listadoRegistros.data" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="lr.nombre_siembra"></td>
                    <td v-text="lr.lote"></td>
                    <td v-text="lr.fecha_registro"></td>
                    <td v-text="lr.especie"></td>
                    <td v-text="lr.nombre_registro"></td>
                    <td>
                      {{ lr.peso_ganado | numeral("0.00") }}
                    </td>
                    <td>
                      {{ lr.biomasa | numeral("0.00") }}
                    </td>
                    <td>
                      {{ lr.biomasa_disponible | numeral("0.00") }}
                    </td>
                    <td>
                      {{ lr.cantidad_actual | numeral("0.00") }}
                    </td>
                    <td>
                      {{ lr.bio_dispo_alimen | numeral("0.00") }}
                    </td>
                    <td>
                      {{ lr.salida_animales | numeral("0.00") }}
                    </td>
                  </tr>
                </tbody>
              </table>
              <pagination :align="'center'" :data="listadoRegistros" :limit="8"
                @pagination-change-page="listarRegistros">
                <span slot="prev-nav">&lt; Anterior</span>
                <span slot="next-nav">Siguiente &gt;</span>
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
        Siembra: "nombre_siembra",
        Lote: "lote",
        "Fecha de registro": "fecha_registro",
        Especie: "especie",
        "Tipo actividad": "nombre_registro",
        "Peso Actual \n (g)": {
          field: "peso_ganado",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
        "KG cosecha": {
          field: "biomasa",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
        "Biomasa muestreo\n (Kg)": {
          field: "biomasa_disponible",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
        "Animales Actuales": {
          field: "cantidad_actual",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
        "Biomasa por alimento": {
          field: "bio_dispo_alimen",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
        "Animales Cosechados": {
          field: "salida_animales",
          callback: (value) => {
            return numeral(value).format("0.00");
          },
        },
      },

      listadoSiembras: [],
      listadoRegistros: {},
      listadoEspecies: [],
      listadoLotes: [],
      listadoEstanques: [],
      // filtros
      f_siembra: "",
      f_lote: "",
      f_estado: "1",
      f_especie: "",
      f_actividad: "",
      f_fecha_d: "",
      f_fecha_h: "",
      f_peso_d: 0,
      f_peso_h: 0,
      id_contenedor: "-1",
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
      return this.listadoRegistros;
    },
    listarRegistros(page = 1) {
      let me = this;

      if (this.f_siembra == "") {
        this.smb = "-1";
      } else {
        this.smb = this.f_siembra;
      }

      if (this.f_estado == "") {
        this.est = "-1";
      } else {
        this.est = this.f_estado;
      }
      if (this.f_lote == "") {
        this.lot = "-1";
      } else {
        this.lot = this.f_lote;
      }
      if (this.f_especie == "") {
        this.f_e = "-1";
      } else {
        this.f_e = this.f_especie;
      }
      if (this.f_actividad == "") {
        this.act = "-1";
      } else {
        this.act = this.f_actividad;
      }
      if (this.f_peso_d == "") {
        this.pesod = "-1";
      } else {
        this.pesod = this.f_peso_d;
      }
      if (this.f_peso_h == "") {
        this.pesoh = "-1";
      } else {
        this.pesoh = this.f_peso_h;
      }
      if (this.f_fecha_d == "") {
        this.fec1 = "-1";
      } else {
        this.fec1 = this.f_fecha_d;
      }
      if (this.f_fecha_h == "") {
        this.fec2 = "-1";
      } else {
        this.fec2 = this.f_fecha_h;
      }

      const data = {
        f_siembra: this.smb,
        f_estado: this.est,
        f_lote: this.lot,
        f_especie: this.f_e,
        f_actividad: this.act,
        f_peso_d: this.pesod,
        f_peso_h: this.pesoh,
        f_fecha_d: this.fec1,
        f_fecha_h: this.fec2,
        id_contenedor: this.id_contenedor,
      };

      axios
        .get(`api/informes-registros?page=${page}`, { params: data })
        .then(function (response) {
          me.listadoRegistros = response.data;
        });
    },
    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
      });
    },
    listarEspecies() {
      let me = this;
      axios.get("api/especies").then(function (response) {
        me.listadoEspecies = response.data;
      });
    },
    listarLotes() {
      let me = this;
      axios.get("api/siembras/listado-lotes").then(function (response) {
        me.listadoLotes = response.data;
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
    this.listarSiembras();
    this.listarRegistros();
    this.listarEspecies();
    this.listarLotes();
    this.listarEstanques();
  },
};
</script>
