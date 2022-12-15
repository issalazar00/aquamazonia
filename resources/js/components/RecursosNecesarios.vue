<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Recursos necesarios</div>

          <div class="card-body">
            <div class="row mb-1">
              <div class="text-right col-md-12">
                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success form-control col-md-2" @click="abrirCrear()">
                  Añadir registro
                </button>
              </div>
              <div class="col-md-12">
                <h2>Filtrar por:</h2>
                <form class="row">
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
                    <label for="t_actividad">Tipo de Actividad:
                    </label>
                    <select class="form-control" id="t_actividad" v-model="t_actividad">
                      <option value="-1" selected>
                        Seleccionar</option>
                      <template v-for="(actividad,
                      index) in listadoActividades">
                        <option :key="index" :value="actividad.id" v-if="actividad.actividad != 'Alimentación'">
                          {{
                              actividad.actividad
                          }}</option>
                      </template>

                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="recurso">Recurso: </label>
                    <select class="form-control" id="recurso" v-model="recurso_s">
                      <option value="-1">
                        Seleccionar</option>
                      <option v-for="(recurso,
                      index) in listadoRecursos" :key="index" v-bind:value="recurso.id">
                        {{ recurso.recurso }}
                      </option>
                    </select>
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Desde: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="fecha_ra1"
                      v-model="fecha_ra1" />
                  </div>
                  <div class="form-group col-md-2">
                    <label for="search">Hasta: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="fecha_ra2"
                      v-model="fecha_ra2" />
                  </div>
                  <div class="form-group col-md-2">
                    <input type="checkbox" class="form-check-input" value="1" v-model="see_all" id="see_all" />
                    <label for="see_all" class="form-check-label">
                      <span></span>
                      Ver todos los registros
                    </label>
                  </div>
                  <div class="form-group col-md-2">
                    <button class="btn btn-primary rounded-circle mt-4" type="button" @click="listar()">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                  <div class="col-md-2">
                    <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                      name="recursos-necesarios.xls" type="xls">
                      <i class="fa fa-fw fa-download"></i>
                      Generar Excel
                    </downloadexcel>
                  </div>
                </form>
              </div>
            </div>
            <div>
              <table class="table table-bordered table-striped table-sticky table-sm">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>
                      Tipo de <br />
                      Actividad
                    </th>
                    <th>Siembras</th>
                    <th>Fecha</th>
                    <th>Minutos hombre</th>
                    <th>Costo minutos hombre</th>
                    <th>Recurso</th>
                    <th>Cantidad</th>
                    <th>Costo</th>
                    <th>Costo Total</th>
                    <th>Detalles</th>
                    <th>Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in listado" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="item.actividad"></td>
                    <td v-text="item.nombre_siembra"></td>
                    <td v-text="item.fecha_ra"></td>
                    <td v-text="item.minutos_hombre"></td>
                    <td>
                      {{ item.total_minutos_hombre | numeral('$0,0.00') }}
                    </td>
                    <td v-text="item.recurso"></td>
                    <td v-text="item.cantidad_recurso"></td>
                    <td>
                      {{ item.costo | numeral('$0,0.00') }}
                    </td>
                    <td>
                      {{ item.costo_total_recurso | numeral('$0,0.00') }}
                    </td>
                    <td v-text="item.detalles"></td>
                    <td>
                      <button class="btn btn-danger" @click="
                        eliminarRegistro(item.id)
                      ">
                        <i class="fas fa-trash"></i>
                      </button>
                      <button class="btn btn-success" @click="editarRegistro(item)">
                        <i class="fas fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                  <tr>
                    <th colspan="4" class="text-right">
                      TOTAL:
                    </th>
                    <th v-text="promedios.tmh"> </th>
                    <th>
                      {{ promedios.ttmh | numeral('$0,0.00') }}
                    </th>
                    <th></th>
                    <th v-text="promedios.tcr"></th>
                    <th>
                      {{ promedios.tc | numeral('$0,0.00') }}
                    </th>
                    <th>
                      {{ promedios.ctr | numeral('$0,0.00') }}
                    </th>
                    <th></th>
                    <th></th>
                  </tr>
                </tbody>
              </table>
            </div>
            <nav v-show="showPagination" class="mt-5 navigation ">
              <ul class="pagination justify-content-center">
                <li class="page-item" v-if="pagination.current_page > 1">
                  <a class="page-link" href="#" @click.prevent="
                    cambiarPagina(
                      pagination.current_page - 1
                    )
                  ">Ant</a>
                </li>
                <li class="page-item" v-for="page in pagesNumber" :key="page"
                  :class="[page == isActived ? 'active' : '']">
                  <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page"></a>
                </li>
                <li class="page-item" v-if="
                  pagination.current_page <
                  pagination.last_page
                ">
                  <a class="page-link" href="#" @click.prevent="
                    cambiarPagina(
                      pagination.current_page + 1
                    )
                  ">Sig</a>
                </li>
              </ul>
            </nav>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal añadir recursos a siembras -->
    <div class="modal fade" id="modalRecursos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">
              Recursos por siembra
            </h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="">Tipo de Actividad</label>
                  <select class="form-control" id="tipo_actividad" v-model="form.tipo_actividad">
                    <option selected>--Seleccionar--</option>
                    <option v-for="(actividad,
                    index) in listadoActividades" :key="index" v-bind:value="actividad.id">{{
      actividad.actividad
  }}</option>
                  </select>
                </div>
                <div class="form-group">
                  <label for="minutos hombre">Fecha</label>
                  <input type="date" class="form-control" id="fecha_ra" aria-describedby="fecha_ra"
                    placeholder="Minutos hombre" v-model="form.fecha_ra" />
                </div>
                <div class="form-group">
                  <label for="minutos hombre">Minutos hombre</label>
                  <input type="number" class="form-control" id="minutos_hombre" step="any"
                    aria-describedby="minutos_hombre" placeholder="Minutos hombre" v-model="form.minutos_hombre" min="0"
                    value="0" />
                </div>
                <div class="form-group">
                  <label for="recurso">Recurso</label>
                  <select class="form-control" id="recurso" v-model="form.id_recurso">
                    <option selected>--Seleccionar--</option>
                    <option v-for="(recurso,
                    index) in listadoRecursos" :key="index" v-bind:value="recurso.id">{{
      recurso.recurso
  }}</option>
                  </select>
                </div>
                <div class="form-row my-2" style="background:#f0ffff;" v-show="form.tipo_actividad == 12">
                  <h5 class="col-12">Calcular tiempo</h5>
                  <div class="form-group col-6">
                    <label for="fecha_inicio">Fecha de inicio:</label>
                    <input class="form-control" type="date" name="fecha_inicio" id="fecha_inicio" v-model="fecha_inicio"
                      value="01-01-2021" />
                  </div>
                  <div class="form-group col-6">
                    <label for="hora_inicio">Hora de inicio:</label>
                    <input class="form-control" type="time" name="hora_inicio" id="hora_inicio" v-model="hora_inicio"
                      value="12:00" />
                  </div>

                  <div class="form-group col-6">
                    <label for="fecha_fin">Fecha de fin:</label>
                    <input class="form-control" type="date" name="fecha_fin" id="fecha_fin" v-model="fecha_fin" />
                  </div>
                  <div class="form-group col-6">
                    <label for="hora_fin">Hora de fin:</label>
                    <input class="form-control" type="time" name="hora_fin" id="hora_fin" v-model="hora_fin"
                      value="12:00" />
                  </div>

                  <button type="button" class="btn btn-primary" @click="calcularDiferenciaTiempo()">
                    Calcular tiempo
                  </button>
                </div>
                <div class="form-group">
                  <label for="cantidad_recurso">Cantidad</label>
                  <input type="number" step="any" class="form-control" id="kg_manana"
                    aria-describedby="cantidad_recurso" placeholder="Cantidad" v-model="form.cantidad_recurso" />
                </div>
                <div class="form-group">
                  <label for="detalles">Detalles</label>
                  <textarea class="form-control" id="detalles" aria-describedby="detalles" placeholder="Detalles"
                    v-model="form.detalles"></textarea>
                </div>
              </div>
              <div class="col-md-6" v-if="editando != 1">
                <h5>
                  Seleccionar siembras
                </h5>
                <span v-if="form.id_siembra.length == ''" class="text-danger mb-3">
                  Debe seleccionar al menos una siembra
                </span>
                <v-select :options="siembrasActivas" label="nombre_siembra" :reduce="(siembra) => siembra.id"
                  v-model="form.id_siembra" multiple />
                <!-- <div v-for="(item, index) in listadoSiembras" :key="index">
                  <input type="checkbox" class="form-check-input" v-bind:value="item.id" v-model="form.id_siembra"
                    :id="'siembra-' + item.id" :name="'siembra-' + item.id" />
                  <label :for="'siembra-' + item.id" class="form-check-label">
                    <span></span>
                    {{ item.nombre_siembra }}
                  </label>
                  <br />
                </div> -->
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cerrar
            </button>
            <button v-if="editando != 1 && form.id_siembra.length != ''" type="button" class="btn btn-primary"
              @click="guardarRecursos()">
              Guardar
            </button>
            <button v-if="editando == 1" type="button" class="btn btn-primary" @click="editarRecursos()">
              Editar
            </button>
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
        "Tipo actividad": "actividad",
        Siembra: "nombre_siembra",
        Fecha: "fecha_ra",
        "Minutos hombre": "minutos_hombre",
        "Costo minutos hombre": "total_minutos_hombre",
        Recurso: "recurso",
        Cantidad: "cantidad_recurso",
        Costo: "costo",
        "Costo total recurso": "costo_total_recurso",
        Detalles: "detalles"
      },
      form: new Form({
        id: "",
        id_siembra: [],
        id_recurso: "",
        id_alimento: "",
        tipo_actividad: "",
        fecha_ra: "",
        minutos_hombre: "",
        cantidad_recurso: "",
        detalles: ""
      }),
      fecha_inicio: "",
      hora_inicio: "07:00",
      fecha_fin: "",
      hora_fin: "07:00",
      t_actividad: "-1",
      fecha_ra1: "",
      fecha_ra2: "",
      f_siembra: "",
      f_estado: "1",
      alimento_s: "",
      recurso_s: "",
      see_all: 0,
      busqueda: "",
      addSiembras: [],
      listado: [],
      promedios: [],
      listadoSiembras: [],
      listadoAlimentos: [],
      listadoActividades: [],
      listadoRecursos: [],
      nombresRecursos: [],
      nombresAlimentos: [],
      offset: 10,
      pagination: {
        total: 0,
        current_page: 0,
        per_page: 0,
        last_page: 0,
        from: 0,
        to: 0
      },
      showPagination: 1,
      editando: 0
    };
  },
  components: {
    downloadexcel
  },
  computed: {
    filteredItems() {
      return this.listadoSiembras.filter((item) => item.estado == this.f_estado);
    },
    siembrasActivas() {
      return this.listadoSiembras.filter((item) => item.estado == 1);
    },
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
    }
  },
  methods: {
    async fetchData() {
      let me = this;
      const response = await this.listado;
      return this.listado;
    },
    abrirCrear() {
      let me = this;
      $("#modalRecursos").modal("show");
    },

    listar(page = 1) {
      let me = this;

      let f_siembra = this.f_siembra == "" ? "-1" : this.f_siembra;
      let f_estado = this.f_estado == "-1" ? "-1" : this.f_estado;
      let search_activity = this.t_actividad == "" ? "-1" : this.t_actividad;
      let see_all = this.see_all == "" ? "0" : this.see_all;
      let recurso_s = this.recurso_s == "" ? "-1" : this.recurso_s;
      let fecha_ra1 = this.fecha_ra1 == "" ? "-3" : this.fecha_ra1;
      let fecha_ra2 = this.fecha_ra2 == "" ? "-1" : this.fecha_ra2;

   
      if (this.recurso_s == "") {
        this.rec = "-1";
      } else {
        this.rec = this.recurso_s;
      }

      const data = {
        page: page,
        f_siembra: f_siembra,
        f_estado: f_estado,
        tipo_actividad: search_activity,
        recurso_s: recurso_s,
        fecha_ra1:fecha_ra1,
        fecha_ra2: fecha_ra2,
        see_all: see_all
      };

      axios
        .get("api/recursos-necesarios", { params: data })
        .then(function (response) {
          if (response.data.pagination) {
            me.showPagination = 1;
            me.listado = response.data.recursosNecesarios.data;
            me.promedios = response.data.promedioRecursos;
            me.pagination = response.data.pagination;
          } else {
            me.showPagination = 0;
            me.listado = response.data.recursosNecesarios;
            me.promedios = response.data.promedioRecursos;
            me.pagination = [];
          }
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
        var auxAlimento = response.data;
        auxAlimento.forEach(
          element =>
            (me.nombresAlimentos[element.id] = element.alimento)
        );
      });
    },
    listarActividades() {
      let me = this;
      axios.get("api/actividades").then(function (response) {
        me.listadoActividades = response.data;
      });
    },
    listarRecursos() {
      let me = this;
      axios.get("api/recursos").then(function (response) {
        me.listadoRecursos = response.data;
        var auxRecurso = response.data;
        auxRecurso.forEach(
          element =>
            (me.nombresRecursos[element.id] = element.recurso)
        );
      });
    },
    checkSiembras() {
      let me = this;
      me.addSiembras({
        id_siembra: this.form.id_siembra
      });
    },
    guardarRecursos() {
      let me = this;
      this.form.post("api/recursos-necesarios").then(({ data }) => {
        me.listar();
        $("#modalRecursos").modal("hide");
      });
    },
    editarRecursos() {
      let me = this;
      this.form
        .put("api/recursos-necesarios/" + this.form.id)
        .then(({ data }) => {
          me.listar();
          $("#modalRecursos").modal("hide");
          me.editando = 0;
        });
    },
    eliminarRegistro(objeto) {
      let me = this;
      Swal.fire({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c7120c",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Aceptar!",
        reverseButtons: true
      }).then(result => {
        if (result.isConfirmed) {
          axios
            .delete("api/recursos-necesarios/" + objeto)
            .then(({ data }) => {
              me.listar();
            });
        }
      });
    },
    editarRegistro(objeto) {
      let me = this;
      console.log(objeto);
      this.form.fill(objeto);
      this.editando = 1;
      $("#modalRecursos").modal("show");
    },
    calcularDiferenciaTiempo() {
      var inicio = new Date(this.fecha_inicio + " " + this.hora_inicio);
      // el evento cuyo tiempo ha transcurrido aquí:
      var fin = new Date(this.fecha_fin + " " + this.hora_fin);
      var transcurso = fin.getTime() - inicio.getTime(); // tiempo en milisegundos
      var tiempoMinutos = transcurso / 60000;
      if (transcurso > 0) {
        this.form.cantidad_recurso = tiempoMinutos;
      }
    },
    cambiarPagina(page) {
      let me = this;
      //Actualiza la página actual
      me.pagination.current_page = page;
      me.listar(page);
    }
  },
  mounted() {
    this.listar(1);
    this.listarSiembras();
    this.listarAlimentos();
    this.listarRecursos();
    this.listarActividades();
  }
};
</script>
