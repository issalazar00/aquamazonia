<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Gestión de Siembras</div>

          <div class="card-body">
            <div class="row mb-1">
              <div class="col-12 text-right">
                <button class="btn btn-success" @click="anadirItem(), id = 0">
                  Nueva siembra
                </button>
              </div>
            </div>
            <div>
              <table class="table table-bordered table-striped table-sm table-sticky">
                <thead class="thead-primary">
                  <tr>
                    <th>#</th>
                    <th>
                      Nombre <br />
                      siembra
                    </th>
                    <th>Contenedor</th>
                    <th class="text-center d-sm-none d-none d-md-block" style="width: 340px">
                      <h5>Especie</h5>
                      <div class="py-3">
                        <li class="" style="width: 80px; display: inline-block">
                          Especie
                        </li>
                        <li class="" style="width: 80px; display: inline-block">
                          Lote
                        </li>
                        <li class="" style="width: 80px; display: inline-block">
                          Cantidad
                        </li>
                        <li class="" style="width: 60px; display: inline-block">
                          Peso gr
                        </li>
                      </div>
                    </th>
                    <th>Inicio siembra</th>
                    <th>Inicio - fin de descanso estanque</th>
                    <th>Fecha Alimentación</th>
                    <th>Ingreso</th>
                    <th>Finalizar</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(siembra, index) in listadoSiembras" :key="siembra.id">
                    <th v-text="index + 1" scope="row"></th>
                    <td v-text="siembra.nombre_siembra" scope="row"></td>
                    <td v-text="siembra.contenedor"></td>
                    <td class="d-sm-none d-none d-md-block">
                      <ul v-for="pez in siembra.peces" :key="pez.id" class="nav border-0">
                        <li v-text="pez.especie" class="nav-item border-bottom"
                          style="width: 80px; display: inline-block">
                          Especie
                        </li>
                        <li v-text="pez.lote" class="nav-item border-bottom" style="width: 80px; display: inline-block">
                          Lote
                        </li>
                        <li v-text="pez.cant_actual" class="nav-item border-bottom"
                          style="width: 80px; display: inline-block">
                          Cantidad
                        </li>
                        <li v-text="pez.peso_actual + 'Gr'" class="nav-item border-bottom"
                          style="width: 60px; display: inline-block">
                          Peso
                        </li>
                      </ul>
                    </td>
                    <td v-text="siembra.fecha_inicio"></td>
                    <td>
                      {{ siembra.ini_descanso }} - <br />
                      {{ siembra.fin_descanso }}
                    </td>

                    <td>
                      <span v-bind:class="[
                        fechaActual <= siembra.fecha_alimento
                          ? ''
                          : 'badge badge-warning',
                      ]">
                        {{ siembra.fecha_alimento }}
                      </span>

                      <button type="button" class="btn btn-success btn-sm" @click="abrirCrear(siembra.id)">
                        Añadir Alimentos
                      </button>
                    </td>
                    <td>
                      <button class="btn btn-primary" @click="abrirIngreso(siembra.id)">
                        <i class="fas fa-list-ul"></i>
                      </button>
                    </td>
                    <td>
                      <button class="btn btn-warning" data-toggle="tooltip" title="Finalizar siembra"
                        data-placement="top" @click="finalizarSiembra(siembra.id)">
                        <i class="fas fa-power-off"></i>
                      </button>
                    </td>
                    <td>
                      <button class="btn btn-success" @click="editarSiembra(siembra)">
                        <i class="fas fa-edit"></i>
                      </button>
                    </td>
                    <td>
                      <button class="btn btn-danger" @click="eliminarSiembra(siembra.id)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <create-edit-stocking :listado-contenedores="listadoContenedores" ref="createEditStocking"
      :listado-especies="listadoEspecies" :id="id" />
    <!-- Modal añadir alimentos a siembras -->
    <div class="modal fade" id="modalRecursos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h3 class="modal-title" id="exampleModalLabel">
              Alimentos por siembra
            </h3>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form class="row" id="editarAlimentacion">
              <!-- <div class="col-md-6"> -->
              <div class="form-group col-md-3">
                <label for="minutos hombre" class="">Fecha</label>
                <input type="date" class="form-control" id="fecha_ra" aria-describedby="fecha_ra"
                  placeholder="Minutos hombre" v-model="form.fecha_ra" />
              </div>

              <div class="form-group col-md-3">
                <label for="alimento" class="">Alimento</label>
                <select class="form-control custom-select" id="alimento" v-model="form.id_alimento">
                  <option>--Seleccionar--</option>
                  <option v-for="(alimento, index) in listadoAlimentos" :key="index" v-bind:value="alimento.id">
                    {{ alimento.alimento }}
                  </option>
                </select>
              </div>
              <div class="form-group col-md-6">
                <label for="detalles" class="">Detalles</label>
                <textarea class="form-control" id="detalles" aria-describedby="detalles" placeholder="Detalles"
                  v-model="form.detalles"></textarea>
              </div>

              <div class="form-group col-md-3">
                <label for="minutos hombre" class="">Minutos hombre</label>
                <input type="number" class="form-control" step="any" id="minutos_hombre"
                  aria-describedby="minutos_hombre" placeholder="Minutos hombre" v-model="form.minutos_hombre" />
              </div>

              <div class="form-group col-md-3">
                <label for="cant_manana" class="">Kg Mañana</label>
                <input type="number" class="form-control" id="kg_manana" aria-describedby="cant_manana"
                  placeholder="Kg Mañana" v-model="form.cant_manana" />
              </div>
              <div class="form-group col-md-3">
                <label for="cant_tarde" class="">Kg tarde</label>
                <input type="number" class="form-control" id="cant_tarde" aria-describedby="cant_tarde"
                  placeholder="Kg tarde" v-model="form.cant_tarde" />
              </div>
              <div class="form-group col-md-4">
                <label for="conv_alimenticia">Conversión alimenticia teórica</label>
                <input type="number" step="any" class="form-control" id="conv_alimenticia"
                  placeholder="Conversión alimenticia teórica" v-model="form.conv_alimenticia" />
              </div>
            </form>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" @click="guardarRecursos()">
                <span v-text="editandoAlimento == 0 ? 'Guardar' : 'Actualizar'"></span>
              </button>
            </div>
            <div class="container">
              <table class="
                  table table-sm table-hover table-responsive table-bordered
                ">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>
                      Tipo de <br />
                      Actividad
                    </th>
                    <th>Fecha</th>
                    <th><br />Alimento</th>
                    <th>Cantidad<br />Mañana</th>
                    <th>Cantidad<br />Tarde</th>
                    <th width="15%">Detalles</th>
                    <th>Editar</th>
                    <th>Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in listadoRN" :key="index">
                    <td v-text="index + 1"></td>
                    <td v-text="item.actividad"></td>
                    <td v-text="item.fecha_ra"></td>
                    <td>{{ item.alimento }}</td>
                    <td v-text="
                      item.cant_manana == null
                        ? '-'
                        : item.cant_manana + ' kg'
                    "></td>
                    <td v-text="
                      item.cant_tarde == null ? '-' : item.cant_tarde + ' kg'
                    "></td>
                    <td v-text="item.detalles"></td>
                    <td>
                      <a href="#editarAlimentacion" class="btn btn-success" @click="editarAlimento(item)">
                        <i class="fas fa-edit"></i>
                      </a>
                    </td>
                    <td>
                      <a class="btn btn-danger" @click="eliminarAlimento(item.id_registro)">
                        <i class="fas fa-trash"></i>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cerrar
            </button>
            <!-- <button type="button" class="btn btn-primary" @click="guardarRecursos()">Guardar</button> -->
          </div>
        </div>
      </div>
    </div>
    <!-- Modal registros -->
    <div class="modal" tabindex="-1" role="dialog" id="modalIngreso">
      <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title text-center col-md-9">Registros</h5>
            <button type="button" class="btn btn-primary" @click="
              ver_registros == 1 ? (ver_registros = 0) : (ver_registros = 1)
            ">
              <span v-if="ver_registros == 1">Crear Registros <i class="fas fa-arrow-right"></i></span>
              <span v-if="ver_registros == 0"><i class="fas fa-arrow-left"></i> Ver listado de
                registros</span>
            </button>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div id="mostrarRegistros" v-if="ver_registros == 1">
              <div class="col-md-12">
                <h5>Filtrar por:</h5>
                <form class="row">
                  <div class="form-group col-md-4">
                    <label for="tipo_registro">Tipo</label>
                    <select class="form-control" id="tipo_registro" v-model="f_actividad">
                      <option value="0">Muestreo</option>
                      <option value="1">Pesca</option>
                      <option value="2">Mortalidad inicial</option>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label for="search">Desde: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="fecha_desde"
                      v-model="f_fecha_d" />
                  </div>
                  <div class="form-group col-md-3">
                    <label for="search">Hasta: </label>
                    <input class="form-control" type="date" placeholder="Search" aria-label="fecha_hasta"
                      v-model="f_fecha_h" />
                  </div>
                  <div class="form-group col-md-2">
                    <button class="btn btn-primary rounded-circle mt-4" @click="filtrarIngresos()" type="button">
                      <i class="fas fa-search"></i>
                    </button>
                  </div>
                </form>
              </div>
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Especie</th>
                    <th>Tipo de registro</th>
                    <th>Fecha</th>
                    <th>Peso ganado (gr)</th>
                    <th>Mortalidad</th>
                    <th>Biomasa(kg)</th>
                    <th>Cantidad</th>
                    <th>Eliminar</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(registro, index) in listadoRegistros" :key="registro.id">
                    <th v-text="index + 1"></th>
                    <td v-text="registro.especie"></td>
                    <td v-text="tipoRegistro[registro.tipo_registro]"></td>
                    <td v-text="registro.fecha_registro"></td>
                    <td v-text="
                      registro.peso_ganado == null
                        ? '-'
                        : registro.peso_ganado + 'gr'
                    "></td>
                    <td v-text="
                      registro.mortalidad == null ? '-' : registro.mortalidad
                    "></td>
                    <td v-text="registro.biomasa == null ? '-' : registro.biomasa"></td>
                    <td v-text="
                      registro.cantidad == null ? '-' : registro.cantidad
                    "></td>
                    <td>
                      <button class="btn btn-danger" @click="eliminarRegistro(registro.id, registro)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div id="crearRegistros" v-if="ver_registros == 0">
              <div class="row">
                <div class="form-group col-sm-6 col-lg-3">
                  <label for="fecha_registro">Fecha Registro</label>
                  <input type="date" class="form-control" id="fecha_registro" placeholder="Fecha"
                    v-model="fecha_registro" />
                </div>
                <div class="form-group col-sm-6 col-lg-3">
                  <label for="tipo_registro">Tipo</label>
                  <select class="form-control" id="tipo_registro" v-model="tipo_registro">
                    <option value="0">Muestreo</option>
                    <option value="1">Pesca</option>
                    <option value="2">Mortalidad inicial</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-12 col-lg-8 mx-auto">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th scope="col">Especie</th>
                      <th scope="col" v-if="tipo_registro == 0">
                        Peso actual (gr)
                      </th>
                      <th scope="col" v-if="tipo_registro == 0">Mortalidad</th>
                      <th scope="col" v-if="tipo_registro == 1">
                        Biomasa (kg)
                      </th>
                      <!-- <th scope="col" v-if="tipo_registro == 1">Cantidad  idSiembraRegistro  </th> -->
                      <th scope="col" v-if="tipo_registro == 2">
                        Mortalidad Inicial
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="pez in pecesxSiembra" :key="pez.id" v-if="pez.id_siembra == idSiembraRegistro">
                      <th scope="row" v-text="pez.especie"></th>
                      <td v-if="tipo_registro == 0">
                        <input type="number" class="form-control" v-bind:required="tipo_registro == 0 ? 'required' : ''"
                          step="any" v-model="
                            campos[pez.id_siembra][pez.id]['peso_ganado']
                          " />
                      </td>
                      <td v-if="tipo_registro == 0 || tipo_registro == 2">
                        <input type="number" id="mortalidad" class="form-control" v-bind:required="
                          tipo_registro == 0 || 2 ? 'required' : ''
                        " v-model="campos[pez.id_siembra][pez.id]['mortalidad']" />
                      </td>
                      <td v-if="tipo_registro == 1">
                        <input type="number" step="any" class="form-control"
                          v-bind:required="tipo_registro == 1 ? 'required' : ''"
                          v-model="campos[pez.id_siembra][pez.id]['biomasa']" />
                      </td>
                      <!-- <td v-if="tipo_registro == 1 ">
                        <input type="number" class="form-control" v-bind:required="tipo_registro == 1 ? 'required' : ''" v-model="campos[pez.id_siembra][pez.id]['cantidad']">
                      </td>                                                  -->
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">
              Cerrar
            </button>
            <button type="button" class="btn btn-primary" v-if="ver_registros == 0"
              @click="crearRegistro(idSiembraRegistro)">
              Crear registro
            </button>
          </div>
        </div>
      </div>
    </div>

    <finish-stocking ref="finishStocking" :id.sync="id" @list-stocking="listar()" />
  </div>
</template>

<script>
import finishStocking from "./finish-stocking.vue";
import createEditStocking from "./create-edit-stocking.vue";
import Vue from "vue";
import { Form, HasError, AlertError } from "vform";

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

export default {
  components: { finishStocking, createEditStocking },
  data() {
    return {
      id: 0,
      form: new Form({
        id: "",
        fecha_inicio: "",
        nombre_siembra: "",
        id_contenedor: "",
        id_siembra: "",
        id_recurso: 0,
        id_registro: "",
        id_alimento: "",
        tipo_actividad: "1",
        fecha_ra: "",
        minutos_hombre: "",
        cant_manana: "",
        cant_tarde: "",
        conv_alimenticia: "",
        detalles: "",
      }),
      editandoAlimento: 0,
      fechaActual: [],
      ver_registros: 1,
      id_edita: "",
      itemRegistro: [],

      listadoEspecies: [],
      listadoContenedores: [],
      listado: [],
      listadoSiembras: [],
      listadoRegistros: [],
      listadoAlimentos: [],
      listadoRN: [],
      pecesxSiembra: [],
      lotes: [],
      // Registros
      id_siembra: "",
      id_especie: "",
      fecha_registro: "",
      tipo_registro: "",
      peso_ganado: "",
      mortalidad: 0,
      biomasa: "",
      cantidad: "",
      aux_lote: "",
      aux_cantidad: "",
      aux_peso_inicial: "",

      id_siembra: "",
      mortalidad_inicial: "",
      idSiembraRegistro: "",
      idSiembraR: "",
      estados: [],
      tipoRegistro: [],
      campos: {
        camps_s: [],
      },
      // Filtros ingresos
      f_siembra: "",
      f_actividad: "",
      f_fecha_d: "",
      f_fecha_h: ""
    };
  },

  methods: {

    guardaEditItem(id) {
      let me = this;
      const data = {
        especie: this.id_edit_item,
        lote: this.aux_lote,
        cantidad: this.aux_cantidad,
        cant_actual: this.aux_cantidad,
        peso_inicial: this.aux_peso_inicial,
      };
      axios.put("api/siembras/" + id, data).then(({ data }) => {
        (this.id_edit_item = ""),
          (this.aux_lote = ""),
          (this.aux_cantidad = ""),
          (this.aux_peso_inicial = "");
      });
    },
    listarEspecies() {
      let me = this;
      axios.get("api/especies").then(function (response) {
        me.listadoEspecies = response.data;
      });
    },
    listarContenedores() {
      let me = this;
      axios.get("api/contenedores").then(function (response) {
        me.listadoContenedores = response.data;
      });
    },

    listarAlimentos() {
      let me = this;
      axios.get("api/alimentos").then(function (response) {
        me.listadoAlimentos = response.data;
        var auxAlimento = response.data;
      });
    },
    anadirItem() {
      $("#modalSiembra").modal("show");
      this.listarEspecies();
      this.listarContenedores();
      this.listadoItems = [];

    },
    editarSiembra(siembra) {
      this.id = siembra.id
      $("#modalSiembra").modal("show");
      this.listarContenedores();
      this.$refs.createEditStocking.editarSiembra(siembra)
    },
    editarAlimento(objeto) {
      let me = this;
      this.editandoAlimento = 1;
      this.form.fill(objeto);
    },

    abrirCrear(id) {
      let me = this;
      $("#modalRecursos").modal("show");
      this.form.id_siembra = id;
      this.idSiembraR = id;

      axios.post("api/siembras-alimentacion/" + id).then(function (response) {
        me.listadoRN = response.data.recursosNecesarios;
      });
    },

    listar() {
      let me = this;
      this.listarEspecies();
      this.listarAlimentos();
      axios.get("api/siembras").then(function (response) {
        me.listadoSiembras = response.data.siembra;
        me.pecesxSiembra = response.data.pecesSiembra;
        me.campos = response.data.campos;

        me.fechaActual = response.data.fecha_actual;
      });
    },
    listarLotes() {
      axios.get("api/siembras/listado-lotes").then(function (response) {
        me.lotes = response.data.lotes;
      })
    },
    abrirIngreso(id) {
      this.idSiembraRegistro = id;
      let me = this;
      this.ver_registros = 1;
      $("#modalIngreso").modal("show");

      this.tipo_registro = 0;
      axios.post("api/registros-siembra/" + id).then(function (response) {
        me.listadoRegistros = response.data;
      });
    },
    crearRegistro(id) {
      let me = this;
      this.ver_registros = 0;
      // this.idSiembraRegistro = id;
      let aux_campos = me.campos[id];

      const data = {
        campos: aux_campos,
        id_siembra: id,
        fecha_registro: this.fecha_registro,
        tipo_registro: this.tipo_registro,
      };
      axios.post("api/registros", data).then(({ response }) => {
        me.aux_campos = [];
        me.ver_registros = 1;
        me.abrirIngreso(id);
        me.listar();
      });
    },
    filtrarIngresos() {
      let me = this;

      // if(this.f_siembra == ''){this.smb = '-1'}else{this.smb = this.f_siembra}
      if (this.f_actividad == "") {
        this.act = "-1";
      } else {
        this.act = this.f_actividad;
      }
      if (this.f_fecha_d == "") {
        this.f_d = "-1";
      } else {
        this.f_d = this.f_fecha_d;
      }
      if (this.f_fecha_h == "") {
        this.f_h = "-1";
      } else {
        this.f_h = this.f_fecha_h;
      }

      const data = {
        f_siembra: this.idSiembraRegistro,
        f_actividad: this.act,
        f_fecha_d: this.f_d,
        f_fecha_h: this.f_h,
      };
      axios.post("api/filtro-registros", data).then((response) => {
        me.listadoRegistros = response.data;
      });
    },
    finalizarSiembra(id) {
      $("#modalFinalizar").modal("show");
      this.id = id;
    },

    guardarRecursos() {
      let me = this;
      if (this.editandoAlimento == 0) {
        axios.post("api/recursos-necesarios", this.form).then(({ data }) => {
          me.listar();
          me.abrirCrear(this.form.id_siembra);
          swal(
            "Excelente!",
            "Los datos se guardaron correctamente!",
            "success"
          );
        });
      } else {
        this.form
          .put("api/recursos-necesarios/" + this.form.id_registro)
          .then(({ data }) => {
            this.form.reset();
            this.editandoAlimento = 0;
            me.abrirCrear(this.form.id_siembra);
          });
      }
    },
    eliminarRegistro(id, objeto) {
      let me = this;
      swal({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          const data = {
            campos: objeto,
          };
          axios.put("api/registros/" + id, data).then(({ data }) => {
            me.abrirIngreso(objeto.id_siembra);
            me.listar();
          });
        }
      });
    },

    eliminarAlimento(objeto) {
      let me = this;
      swal({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.delete("api/recursos-necesarios/" + objeto).then(({ data }) => {
            me.abrirCrear(this.idSiembraR);
            me.listar();
          });
        }
      });
    },

    eliminarSiembra(index) {
      let me = this;
      swal({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.delete("api/siembras/" + index).then(({ data }) => {
            me.listar();
          });
        }
      });
    },
  },
  mounted() {
    this.listar();
    this.estados[0] = "Inactivo";
    this.estados[1] = "Activo";
    this.estados[2] = "Ocupado";
    this.estados[3] = "Descanso";
    this.tipoRegistro[0] = "Muestreo";
    this.tipoRegistro[1] = "Pesca";
    this.tipoRegistro[2] = "Mortalidad Inicial";
  },
};
</script>
