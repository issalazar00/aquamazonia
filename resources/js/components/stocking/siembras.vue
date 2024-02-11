<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Gestión de Siembras</div>

          <div class="card-body">
            <div class="row mb-1">
              <div class="col-12 text-right">
                <button class="btn btn-success" @click="anadirItem(), (id = 0)">
                  Nueva siembra
                </button>
              </div>
            </div>
            <div class="row">
              <div class="form-row col-12">
                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                  <label for="estado_siembta">Estado de siembra</label>
                  <select name="estado_siembra" class="custom-select" id="estado_siembra" v-model="estado_siembra">
                    <option value="-1">Todas</option>
                    <option value="1">Activas</option>
                    <option value="0">Inactivas</option>
                  </select>
                </div>
                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                  <label for="siembra_activa">Siembras
                    {{ (estado_siembra == "-1") ? "" : (estado_siembra == 0 ? "inactivas" : "activas") }} :
                  </label>
                  <template v-if="estado_siembra != '-1'">
                    <v-select :options="filteredItems" label="nombre_siembra" :reduce="(siembra) => siembra.id"
                      v-model="f_siembra" />
                  </template>
                  <template v-if="estado_siembra == '-1'">
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
                  <label for="siembra_activa">Contenedor :
                  </label>
                  <template>
                    <v-select :options="listadoContenedores" label="contenedor" :reduce="(contenedor) => contenedor.id"
                      v-model="contenedor_id" />
                  </template>
                </div>
                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                  <label for="search_nro_results">Mostrar {{ search_nro_results }} por página</label>
                  <input type="number" v-model="search_nro_results" class="form-control">
                </div>
                <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                  <button class="btn btn-success btn-block mt-4" @click="listar()">Buscar</button>
                </div>
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
                    <th>Estanque</th>
                    <th class="text-center d-sm-none d-none d-md-block" style="">
                      <h5>Especie</h5>

                      <div class="py-3" style="width: max-content; margin: auto">
                        <span style="width: 80px; display: inline-block">
                          Especie
                        </span>
                        <span style="width: 80px; display: inline-block">
                          Lote
                        </span>
                        <span style="width: 80px; display: inline-block">
                          Cantidad
                        </span>
                        <span style="width: 60px; display: inline-block">
                          Peso gr
                        </span>
                      </div>
                    </th>
                    <th>Inicio siembra</th>

                    <th>Fecha Alimentación</th>
                    <th>Ingreso</th>

                    <th>Editar</th>
                    <th>Eliminar</th>
                    <th>Finalizar</th>
                    <th>
                      Inicio - fin de <br />
                      descanso estanque
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(siembra) in listado.data" :key="siembra.id">
                    <th v-text="siembra.id" scope="row"></th>
                    <td>
                      {{ siembra.nombre_siembra }} <br>
                      <small v-if="siembra.phase_id">
                        Fase: <b>{{ siembra.phase.phase }}</b>
                      </small> <br>
                      <small v-if="siembra.tipo">
                        Tipo: <b>{{ siembra.tipo }}</b>
                      </small>
                    </td>
                    <td v-text="siembra.contenedor"></td>
                    <td class="d-sm-none d-none d-md-block">
                      <div v-for="pez in siembra.peces" :key="pez.id" class="border-0"
                        style="width: max-content; margin: auto">
                        <span class="nav-item border-bottom" style="width: 80px; display: inline-block">
                          {{ pez.especie.especie }}
                        </span>
                        <span class="nav-item border-bottom"
                          style="width: 80px;text-align: center;display: inline-block;">
                          {{ pez.lote }}
                        </span>
                        <span class="nav-item border-bottom"
                          style="width: 80px;text-align: center;display: inline-block;">
                          {{ Math.floor(pez.cant_actual) }}
                        </span>
                        <span class="nav-item border-bottom"
                          style="width: 60px;display: inline-block;text-align: center;">
                          {{ pez.peso_actual + "Gr" }}
                        </span>
                      </div>
                    </td>
                    <td v-text="siembra.fecha_inicio"></td>

                    <td>
                      <span v-bind:class="[
                        fechaActual <= siembra.fecha_alimento
                          ? ''
                          : 'badge badge-warning',
                      ]">
                        {{ siembra.fecha_alimento }}
                      </span>
                      <br />
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
                      <button class="btn btn-success" @click="editarSiembra(siembra)">
                        <i class="fas fa-edit"></i>
                      </button>
                    </td>
                    <td>
                      <button class="btn btn-danger" @click="eliminarSiembra(siembra.id)">
                        <i class="fas fa-trash"></i>
                      </button>
                    </td>
                    <td>
                      <button v-if="siembra.estado == 1" class="btn btn-danger text-white" data-toggle="tooltip"
                        title="Finalizar siembra" data-placement="top" @click="finalizarSiembra(siembra.id)">
                        <i class="fas fa-power-off"></i> Desactivar
                      </button>
                      <button v-if="siembra.estado == 0" class="btn btn-success text-white" data-toggle="tooltip"
                        title="Finalizar siembra" data-placement="top" @click="activarSiembra(siembra.id)">
                        <i class="fas fa-power-on"></i> Activar
                      </button>
                    </td>
                    <td>
                      {{ siembra.ini_descanso }} - <br />
                      {{ siembra.fin_descanso }}
                    </td>
                  </tr>
                </tbody>
              </table>
              <pagination :align="'center'" :data="listado" :limit="2" @pagination-change-page="listar">
                <span slot="prev-nav"><i class="fas fa-arrow-left"></i></span>
                <span slot="next-nav"><i class="fas fa-arrow-right"></i></span>
              </pagination>
            </div>
          </div>
        </div>
      </div>
    </div>
    <create-edit-stocking :listado-contenedores="listadoContenedores" ref="createEditStocking"
      :listado-especies="listadoEspecies" :id="id" />
    <dialog-fish-food :siembra_id="id" ref="dialogFishFood"></dialog-fish-food>
    <dialog-registers :siembra_id="id" ref="dialogRegister"></dialog-registers>
    <finish-stocking ref="finishStocking" :id.sync="id" @list-stocking="listar()" />
  </div>
</template>

<script>
import finishStocking from "./finish-stocking.vue";
import createEditStocking from "./create-edit-stocking.vue";
import DialogRegisters from "./dialog-registers.vue";
import DialogFishFood from "./dialog-fish-food.vue";
import { Form, HasError, AlertError } from "vform";


export default {
  components: {
    finishStocking,
    createEditStocking,
    DialogRegisters,
    DialogFishFood,
  },
  data() {
    return {
      id: 0,
      form: new Form({
        nombre_siembra: "",
        id_siembra: "",
      }),
      listPhases: [],
      fechaActual: [],
      listadoEspecies: [],
      listadoContenedores: [],
      listado: {},
      listadoSiembras: [],
      listadoRN: [],
      lotes: [],
      estados: [],
      tipoRegistro: [],

      //Filtro siembras
      estado_siembra: "-1",
      f_siembra: "",
      contenedor_id: "-1",
      search_phase: "",
      search_type: "",
      search_nro_results:"15"
    };
  },
  computed: {

    filteredItems() {
      return this.listadoSiembras.filter((item) => item.estado == this.estado_siembra);
    }
  },
  methods: {
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

    anadirItem() {
      $("#modalSiembra").modal("show");
      this.listarEspecies();
      this.listarContenedores();
      this.listadoItems = [];
    },
    editarSiembra(siembra) {
      this.id = siembra.id;
      $("#modalSiembra").modal("show");
      this.listarContenedores();
      this.$refs.createEditStocking.editarSiembra(siembra);
      this.listarEspecies();
    },

    abrirCrear(id) {
      $("#modalRecursos").modal("show");
      this.id = id;
      this.$refs.dialogFishFood.listarRecursosAlimentos(id);
    },

    listar(page=1) {
      let me = this;
      let data = {
        estado_siembra: me.estado_siembra,
        id_siembra: me.f_siembra,
        contenedor_id: me.contenedor_id,
        phase: this.search_phase,
        page: page,
        type: this.search_type,
        nro_results:this.search_nro_results
      }

      axios
        .get(
          "api/siembras",
          { params: data }
        )
        .then(function (response) {
          me.listado = response.data.siembra;
          me.fechaActual = response.data.fecha_actual;
        });
    },
    listarSiembras() {
      let me = this;
      axios.get("api/siembras/listado").then(function (response) {
        me.listadoSiembras = response.data;
      });
    },

    listarLotes() {
      let me = this;
      axios.get("api/siembras/listado-lotes").then(function (response) {
        me.lotes = response.data.lotes;
      });
    },
    abrirIngreso(id) {
      this.id = id;
      $("#modalIngreso").modal("show");
      this.$refs.dialogRegister.listarRegistros(id);
    },

    finalizarSiembra(id) {
      $("#modalFinalizar").modal("show");
      this.id = id;
    },

    activarSiembra(id) {
      axios.post(`api/siembras/cambiar-estado/${id}`, null)
        .then(({ response }) => {
          Swal.fire({
            title: "Exitoso",
            text: "El estado se ha cambiado correctamente",
            icon: "success",
          })
          this.listar()
        });
    },

    eliminarSiembra(index) {
      let me = this;
      Swal.fire({
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
    },
  },
  mounted() {
    this.listar(1, "");
    this.listarSiembras();
    this.listarContenedores();
    this.listarLotes();
    this.estados[0] = "Inactivo";
    this.estados[1] = "Activo";
    this.estados[2] = "Ocupado";
    this.estados[3] = "Descanso";
  },
};
</script>
