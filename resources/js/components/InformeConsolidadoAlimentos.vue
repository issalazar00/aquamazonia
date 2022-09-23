<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Informes consolidado de alimentos</div>
          <div class="card-body">
            <div class="row">
              <div class="form-row col-12">
                <div class="form-group col-3">
                  <label for="estado_siembta">Estado de siembra</label>
                  <select name="f_estado" class="custom-select" id="f_estado" v-model="f_estado">
                    <option value="-1">Todas</option>
                    <option value="1">Activas</option>
                    <option value="0">Inactivas</option>
                  </select>
                </div>
                <div class="form-group col-3">
                  <label for="siembra_activa">Siembras
                    {{ f_estado == 0 ? "inactivas" : "activas" }} :</label>
                  <select name="siembra_activa" class="custom-select" id="siembra_activa" v-model="f_siembra"
                    v-if="f_estado != '-1'">
                    <template v-for="(siembra, index) in listadoSiembras">
                      <option v-if="siembra.estado == f_estado" :key="index" :value="siembra.id">
                        {{ siembra.nombre_siembra }}
                      </option>
                    </template>
                  </select>
                  <select name="siembra_activa" class="custom-select" id="siembra_activa" v-model="f_siembra"
                    v-if="f_estado == '-1'">
                    <template>
                      <option v-for="(siembra, index) in listadoSiembras" :key="index" :value="siembra.id">
                        {{ siembra.nombre_siembra }}
                      </option>
                    </template>
                  </select>
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
                  <label for="alimento">Alimento: </label>
                  <select class="form-control" id="alimento" v-model="id_alimento">
                    <option selected value="-1">Seleccionar</option>
                    <option v-for="(alimento, index) in listadoAlimentos" :key="index" v-bind:value="alimento.id">
                      {{ alimento.alimento }}
                    </option>
                  </select>
                </div>
                <div class="form-group col-md-3 offset-9">
                  <button class="btn btn-primary" @click="listar()">
                    Filtrar resultados
                  </button>
                </div>
              </div>

              <div class="col-md-2">
                <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                  name="informe-consolidado-alimentos.xls" type="xls">
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
                    <th>Estado</th>
                    <th>Tipo actividad</th>
                    <th>Alimento</th>
                    <th>Costo Kg</th>
                    <th>Alimento Mañana (kg)</th>
                    <th>Alimento Tarde (kg) </th>
                    <th>Cantidad Total Alimento (kg) </th>
                    <th>% Cantidad Alimento</th>
                    <th>Costo Alimento</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(lrn, index) in listado" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="lrn.nombre_siembra"></td>
                    <td v-if="lrn.estado == 1">Activa</td>
                    <td v-else>Inactiva</td>
                    <td>
                      {{
                      lrn.tipo_actividad == 1
                      ? (lrn.actividad = "Alimentacion")
                      : ""
                      }}
                    </td>
                    <td>{{ lrn.alimento }}</td>
                    <td>$ {{ lrn.costoUnitarioAlimento }}</td>
                    <td>{{ lrn.c_manana }}</td>
                    <td>{{ lrn.c_tarde }}</td>
                    <th v-text="lrn.cantidadTotalAlimento"></th>
                    <td class="text-right" v-text="lrn.porcCantidadAlimento + '%'"></td>
                    <th class="text-right">$ {{ lrn.costoAlimento }}</th>
                  </tr>
                </tbody>
              </table>
              <nav class="mt-5 navigation">
                <ul class="pagination justify-content-center">
                  <li class="page-item" v-if="pagination.current_page > 1">
                    <a class="page-link" href="#" @click.prevent="
                      cambiarPagina(pagination.current_page - 1)
                    ">Ant</a>
                  </li>
                  <li class="page-item" v-for="page in pagesNumber" :key="page"
                    :class="[page == isActived ? 'active' : '']">
                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page"></a>
                  </li>
                  <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                    <a class="page-link" href="#" @click.prevent="
                      cambiarPagina(pagination.current_page + 1)
                    ">Sig</a>
                  </li>
                </ul>
              </nav>
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
        Alimento: "alimento",
        "Costo Kg": "costoUnitarioAlimento",
        "Alimento Mañana\n (Kg)": "c_manana",
        "Alimento Tarde\n (Kg)": "c_tarde",
        "Cantidad total alimento \n (Kg)": "cantidadTotalAlimento",
        "% Cantidad Alimento": "porcCantidadAlimento",
        "Costo total alimento": "costoAlimento",
      },
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0,
      },
      offset: 10,
      tipoActividad: "",
      id_alimento: "-1",
      f_siembra: "-1",
      f_estado: '1',
      id_contenedor: '-1',
      listado: [],
      listadoSiembras: [],
      listadoAlimentos: [],
      listadoEstanques: []
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
      let me = this;
      const response = await this.listado;
      return this.listado;
    },
    listar(page = 1) {
      let me = this;

      const data = {
        'id_siembra': this.f_siembra == '-1' ? '-1' : this.f_siembra,
        'id_contenedor': this.id_contenedor == '-1' ? '-1' : this.id_contenedor,
        'estado_siembra': this.f_estado == '-1' ? '-1' : this.f_estado,
        'id_alimento': this.id_alimento == '-1' ? '-1' : this.id_alimento,
        'page': page == '-1' ? '-1' : page,
      };

      axios
        .get(
          "api/informes-alimentos",
          { params: data }
        )
        .then(function (response) {
          me.listado = response.data.recursosNecesarios.data;
          me.pagination = response.data.pagination;
        });
    },
    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
      });
    },
    listarAlimentos() {
      let me = this;
      axios.get("api/alimentos").then(function (response) {
        me.listadoAlimentos = response.data;
      });
    },
    listarEstanques() {
      let me = this;
      axios.get("api/contenedores").then(function (response) {
        me.listadoEstanques = response.data;
      });
    },
    cambiarPagina(page) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      me.listar(page);
    },
  },
  mounted() {
    this.listar(1, "", -1, "", "");
    this.listarSiembras(1);
    this.listarAlimentos();
    this.listarEstanques();
  },
};
</script>
