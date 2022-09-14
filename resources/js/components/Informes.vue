<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            Informes recursos y actividades Aquamazonia
          </div>
          <!-- <a href="informe-excel"><button type="submit" class="btn btn-success" name="infoSiembras"><i class="fa fa-fw fa-download"></i> Generar Excel </button></a> -->
          <div class="card-body">
            <div class="row mb-1">
              <div class="col-md-12">
                <h2>Filtrar por:</h2>
                <form
                  class="row"
                  method="POST"
                  action="informe-excel"
                  target="_blank"
                >
                  <div class="form-group col-md-2">
                    <label for="Estado">Estado siembra </label>
                    <select
                      class="form-control"
                      id="estado_s"
                      v-model="estado_s"
                      name="estado_s"
                    >
                      <option value="-1">Todos</option>
                      <option value="0">Inactivo</option>
                      <option value="1" selected>Activo</option>
                    </select>
                  </div>
                  <div class="form-group col-3">
                    <label for="siembra_activa"
                      >Siembras
                      {{ estado_s == 0 ? "inactivas" : "activas" }} :</label
                    >
                    <select
                      name="siembra_activa"
                      class="custom-select"
                      id="siembra_activa"
                      v-model="f_siembra"
                      v-if="estado_s != '-1'"
                    >
                      <template v-for="(siembra, index) in listadoSiembras">
                        <option
                          v-if="siembra.estado == estado_s"
                          :key="index"
                          :value="siembra.id"
                        >
                          {{ siembra.nombre_siembra }}
                        </option>
                      </template>
                    </select>
                    <select
                      name="siembra_activa"
                      class="custom-select"
                      id="siembra_activa"
                      v-model="f_siembra"
                      v-if="estado_s == '-1'"
                    >
                      <template>
                        <option
                          v-for="(siembra, index) in listadoSiembras"
                          :key="index"
                          :value="siembra.id"
                        >
                          {{ siembra.nombre_siembra }}
                        </option>
                      </template>
                    </select>
                  </div>

                  <div class="form-group col-md-2">
                    <label for="contenedor">Estanque:</label>
                    <select
                      class="custom-select"
                      id="contenedor"
                      v-model="f_contenedor"
                    >
                      <option value="-1">Seleccionar</option>
                      <option
                        :value="cont.id"
                        v-for="(cont, index) in listadoEstanques"
                        :key="index"
                      >
                        {{ cont.contenedor }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="actividad">Tipo actividad: </label>
                    <select
                      class="form-control"
                      id="actividad"
                      v-model="actividad_s"
                      name="tipo_actividad"
                      @click="cambiarActividad()"
                    >
                      <option value="-1" selected>Seleccionar</option>
                      <option
                        v-for="(actividad, index) in listadoActividades"
                        :key="index"
                        v-bind:value="actividad.id"
                      >
                        {{ actividad.actividad }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="alimento">Alimento: </label>
                    <select
                      class="form-control"
                      id="alimento"
                      v-model="alimento_s"
                    >
                      <option value="-1" selected>Seleccionar</option>
                      <option
                        v-for="(alimento, index) in listadoAlimentos"
                        :key="index"
                        v-bind:value="alimento.id"
                      >
                        {{ alimento.alimento }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="recurso">Recurso: </label>
                    <select
                      class="form-control"
                      id="recurso"
                      v-model="recurso_s"
                    >
                      <option value="-1" selected>Seleccionar</option>
                      <option
                        v-for="(recurso, index) in listadoRecursos"
                        :key="index"
                        v-bind:value="recurso.id"
                      >
                        {{ recurso.recurso }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Desde: </label>
                    <input
                      class="form-control"
                      type="date"
                      placeholder="Search"
                      aria-label="fecha_ra1"
                      v-model="fecha_ra1"
                    />
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Hasta: </label>
                    <input
                      class="form-control"
                      type="date"
                      placeholder="Search"
                      aria-label="fecha_ra2"
                      v-model="fecha_ra2"
                    />
                  </div>
                  <div class="form-group col-md-1">
                    <label for="">Buscar</label>
                    <button
                      class="btn btn-primary form-control"
                      type="button"
                      @click="listar()"
                    >
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                  <div class="form-group col-md-2">
                    <downloadexcel
                      class="btn btn-success form-control"
                      :fetch="fetchData"
                      :fields="json_fields"
                      name="informe-recursos.xls"
                      type="xls"
                    >
                      <i class="fa fa-fw fa-download"></i> Generar Excel
                    </downloadexcel>
                  </div>
                </form>
              </div>
            </div>
            <div class="table-responsive">
              <table
                class="table table-bordered table-hover table-sticky table-sm"
              >
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>Siembra</th>
                    <th>Estado siembras</th>
                    <th>Tipo <br />actividad</th>
                    <th>Fecha</th>
                    <th>Minutos <br />hombre</th>
                    <th>Costo minutos</th>

                    <template v-if="tipoActividad != 'Alimentación'">
                      <th>Recursos</th>
                      <th>Cantidad</th>
                      <th>Costo Recurso</th>
                      <!-- <th >Costo acumulado Recurso</th> -->
                    </template>
                    <template v-if="tipoActividad == 'Alimentación'">
                      <th>Alimentos</th>
                      <th>Cantidada Mañana (KG)</th>
                      <th>Cantidada Tarde (KG)</th>
                      <th>Costo Alimento</th>
                      <!-- <th>Costo <br>Acumulado</th> -->
                    </template>
                    <th>Costo actividad</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(lrn, index) in listadorn" :key="index">
                    <th v-text="index + 1"></th>
                    <td v-text="lrn.nombre_siembra"></td>
                    <td v-text="estados[lrn.estado]"></td>
                    <td v-text="lrn.actividad"></td>
                    <td v-text="lrn.fecha_ra"></td>
                    <td v-text="lrn.minutos_hombre + 'min'"></td>
                    <td>{{ lrn.costo_minutosh | numeral("$0,0.00") }}</td>
                    <template v-if="tipoActividad != 'Alimentación'">
                      <td v-text="lrn.recurso"></td>
                      <td v-text="lrn.cantidad_recurso"></td>
                      <td>
                        {{ lrn.costo_total_recurso | numeral("$0,0.00") }}
                      </td>
                      <!-- <th v-text="lrn.costo_r_acum"></th> -->
                    </template>

                    <template v-if="tipoActividad == 'Alimentación'">
                      <td v-text="lrn.alimento"></td>
                      <td v-text="lrn.cant_manana"></td>
                      <td v-text="lrn.cant_tarde"></td>
                      <th>
                        {{ lrn.costo_total_alimento | numeral("$0,0.00") }}
                      </th>
                      <!-- <th v-text="lrn.costo_a_acum" ></th> -->
                      <th>
                        {{ lrn.costo_total_actividad | numeral("$0,0.00") }}
                      </th>
                    </template>
                  </tr>
                </tbody>
                <tfoot>
                  <!-- <tr>
                    <th colspan="4" class="text-right">TOTAL: </th>
                    <td colspan="2" class="text-right">Costo minutos: </td>
                    <th>{{ calcularTotalMinutos }}</th>
                    <td colspan="2" v-if="tipoActividad != 'Alimentación'" class="text-right">Costo recursos: </td>
                    <th v-if="tipoActividad != 'Alimentación'">{{ calcularTotalRecursos }}</th>
                    <td colspan="3" v-if="tipoActividad == 'Alimentación'">Costo alimentos: </td>
                    <th v-if="tipoActividad == 'Alimentación'">{{ calcularTotalAlimentos }}</th>
                    <th>Costo total actividades: <br> {{ calcularTotalActividades }}</th>
                  </tr> -->
                </tfoot>
              </table>
            </div>
            <nav v-show="showPagination" class="mt-5 navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item" v-if="pagination.current_page > 1">
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="cambiarPagina(pagination.current_page - 1)"
                    >Ant</a
                  >
                </li>
                <li
                  class="page-item"
                  v-for="page in pagesNumber"
                  :key="page"
                  :class="[page == isActived ? 'active' : '']"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="cambiarPagina(page)"
                    v-text="page"
                  ></a>
                </li>
                <li
                  class="page-item"
                  v-if="pagination.current_page < pagination.last_page"
                >
                  <a
                    class="page-link"
                    href="#"
                    @click.prevent="cambiarPagina(pagination.current_page + 1)"
                    >Sig</a
                  >
                </li>
              </ul>
            </nav>
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
        "Nombre Siembra": "nombre_siembra",
        "Estado Siembra": "estado",
        "Tipo de Actividad": "actividad",
        "Fecha Registro": "fecha_ra",
        "Minutos hombre": "minutos_hombre",
        "Costo minutos hombre": "costo_minutosh",
        Recurso: "recurso",
        "Cantidad Recurso": "cantidad_recurso",
        "Costo Recurso": "costo_total_recurso",
        Alimento: "alimento",
        "Cantidad KG mañana": "cant_manana",
        "Cantidad KG tarde": "cant_tarde",
        "Costo Alimento": "costo_total_alimento",
        "Costo Actividad": "costo_total_actividad",
      },
      listadorn: [],
      listadoActividades: [],
      listadoAlimentos: [],
      listadoSiembras: [],
      listadoRecursos: [],
      listadoEstanques: [],
      imprimirRecursos: [],
      estados: [],
      f_siembra: "",
      f_contenedor: "",
      estado_s: "1",
      actividad_s: "",
      alimento_s: "",
      recurso_s: "",
      fecha_ra1: "",
      fecha_ra2: "",
      costo_acum: 0,
      tipoActividad: "",
      //Paginación
      offset: 10,
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      showPagination: 1,
    };
  },
  components: {
    downloadexcel,
  },
  computed: {
    isActived: function () {
      return this.pagination.current_page;
    },
    //Calcula los elementos de la paginación
    pagesNumber: function () {
      if (!this.pagination.to) {
        return [];
      }

      var from = this.pagination.current_page - this.offset;
      if (from < 1) {
        from = 1;
      }

      var to = from + this.offset * 2;
      if (to >= this.pagination.last_page) {
        to = this.pagination.last_page;
      }

      var pagesArray = [];
      while (from <= to) {
        pagesArray.push(from);
        from++;
      }
      return pagesArray;
    },
  },
  methods: {
    async fetchData() {
      return this.listadorn;
    },
    cambiarActividad() {
      if (this.actividad_s == 1) {
        this.tipoActividad = "Alimentación";
      } else this.tipoActividad = "";
    },
    listar(page) {
      let me = this;
      if (this.f_siembra == "") {
        this.smb = "-1";
      } else {
        this.smb = this.f_siembra;
      }
      if (this.estado_s == "") {
        this.est = "-1";
      } else {
        this.est = this.estado_s;
      }
      if (this.f_contenedor == "") {
        this.cont = "-1";
      } else {
        this.cont = this.f_contenedor;
      }
      if (this.actividad_s == "") {
        this.act = "-1";
      } else {
        this.act = this.actividad_s;
      }
      if (this.alimento_s == "") {
        this.ali = "-1";
      } else {
        this.ali = this.alimento_s;
      }
      if (this.recurso_s == "") {
        this.rec = "-1";
      } else {
        this.rec = this.recurso_s;
      }
      if (this.fecha_ra1 == "") {
        this.fec1 = "-1";
      } else {
        this.fec1 = this.fecha_ra1;
      }
      if (this.fecha_ra2 == "") {
        this.fec2 = "-1";
      } else {
        this.fec2 = this.fecha_ra2;
      }

      axios
        .get("api/informes", {
          params: {
            f_siembra: this.smb,
            estado_s: this.est,
            f_contenedor: this.cont,
            actividad_s: this.act,
            alimento_s: this.ali,
            recurso_s: this.rec,
            fecha_ra1: this.fec1,
            fecha_ra2: this.fec2,
            page: page,
          },
        })
        .then(function (response) {
          me.listadorn = response.data.recursosNecesarios.data;
          me.pagination = response.data.pagination;
        });
    },
    cambiarPagina(page) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      me.listar(page);
    },
    listarActividades() {
      let me = this;
      axios.get("api/actividades").then(function (response) {
        me.listadoActividades = response.data;
      });
    },
    listarAlimentos() {
      let me = this;
      axios.get("api/alimentos").then(function (response) {
        me.listadoAlimentos = response.data;
      });
    },
    listarRecursos() {
      let me = this;
      axios.get("api/recursos").then(function (response) {
        me.listadoRecursos = response.data;
      });
    },
    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
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
    this.listar(1);
    this.listarSiembras();
    this.listarAlimentos();
    this.listarRecursos();
    this.listarActividades();
    this.listarEstanques();
    this.estados[0] = "Inactivo";
    this.estados[1] = "Activo";
  },
};
</script>
