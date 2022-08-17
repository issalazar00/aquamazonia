<template>
  <!-- Modal especies x siembras -->
  <div
    class="modal fade"
    id="modalSiembra"
    tabindex="-1"
    data-backdrop="static"
    role="dialog"
    aria-labelledby="modalSiembraLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSiembralLabel">Crear siembra</h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container row">
            <div class="form-group row col-md-4">
              <div class="col-sm-12 col-md-12 text-left">
                <label for="">Contenedor</label>
                <select
                  v-model="form.id_contenedor"
                  name="id_contenedor"
                  class="form-control"
                  id="id_contenedor"
                  v-if="!id"
                >
                  <template v-for="(contenedor, index) in listadoContenedores">
                    <option
                      v-if="contenedor.estado == 1"
                      :value="contenedor.id"
                      :key="index"
                      selected
                    >
                      {{ contenedor.contenedor }}
                    </option>
                  </template>
                </select>
                <input
                  type="text"
                  class="form-control"
                  disabled
                  readonly
                  v-if="id"
                  :value="form.contenedor"
                />
              </div>
            </div>
            <div class="form-group row col-md-4">
              <div class="col-sm-12 col-md-12 text-left">
                <label for="nombre_siembra">Nombre de Siembra</label>
                <input
                  :disabled="id ? true : false"
                  class="form-control"
                  type="text"
                  id="nombre_siembra"
                  v-model="form.nombre_siembra"
                />
              </div>
            </div>
            <div class="form-group row col-md-4">
              <div class="col-sm-12 col-md-12 text-left">
                <label for="">Fecha Inicio</label>
                <input
                  :disabled="id ? true : false"
                  type="date"
                  class="form-control"
                  id="fecha_inicio"
                  v-model="form.fecha_inicio"
                  required
                />
              </div>
            </div>
            <div class="form-group row col-md-4">
              <div class="col-sm-12 col-md-12 text-left">
                <label for="">Fase</label>
                <select
                  v-model="form.phase_id"
                  name="phase_id"
                  class="form-control"
                  id="phase_id"
                >
                  <option
                    v-for="(phase, index) in phaseListing.data"
                    :value="phase.id"
                    :key="index"
                    selected
                  >
                    {{ (form.phase = phase.phase) }}
                  </option>
                </select>
              </div>
            </div>
            <div class="form-group row col-md-4">
              <div class="col-sm-12 col-md-12 text-left">
                <label for="">Tipo de cultivo</label>
                <select
                  v-model="form.tipo"
                  name="tipo"
                  class="form-control"
                  id="tipo"
                >
                  <option value="Monocultivo" selected>Monocultivo</option>
                  <option value="Policultivo">Policultivo</option>
                </select>
              </div>
            </div>
            <table class="table">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col" style="width: 20%">Especie</th>
                  <th scope="col">Lote</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Peso gr</th>
                  <th scope="col">Añadir</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <th scope="row" v-text="form.id"></th>
                  <td>
                    <select
                      v-model="newEspecie"
                      name=""
                      class="form-control"
                      id="id_especie"
                      required
                    >
                      <option
                        :value="especie.id"
                        v-for="especie in listadoEspecies"
                        :key="especie.id"
                        selected
                      >
                        {{ especie.especie }}
                      </option>
                    </select>
                  </td>
                  <td>
                    <input
                      type="text"
                      min="0"
                      class="form-control"
                      id="lote"
                      v-model="newLote"
                      required
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      min="0"
                      class="form-control"
                      id="cantidad"
                      v-model="newCantidad"
                      required
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      min="0"
                      class="form-control"
                      id="peso_inicial"
                      v-model="newPeso"
                      required
                    />
                    <span
                      style="
                        position: relative;
                        float: right;
                        right: 30px;
                        color: #ccc;
                        bottom: 30px;
                      "
                      >Gr
                    </span>
                  </td>

                  <td>
                    <button
                      class="btn btn-success"
                      @click="anadirEspecie()"
                      type="button"
                    >
                      <i class="fas fa-plus"></i>
                    </button>
                  </td>
                </tr>
                <tr v-for="(item, index) in listadoItems" :key="index">
                  <th scope="row">{{ index + 1 }}</th>
                  <td v-text="nombresEspecies[item.id_especie]"></td>
                  <td>
                    <span v-if="id_edit_item == ''" v-text="item.lote"></span>
                    <input
                      v-if="id_edit_item == item.id_especie"
                      type="text"
                      class="form-control"
                      name="aux_lote"
                      id="aux_lote"
                      v-model="aux_lote"
                    />
                  </td>
                  <td>
                    <span
                      v-if="id_edit_item == ''"
                      v-text="item.cantidad"
                    ></span>
                    <input
                      type="number"
                      class="form-control"
                      v-if="id_edit_item == item.id_especie"
                      name="aux_cantidad"
                      id="aux_cantidad"
                      v-model="aux_cantidad"
                    />
                  </td>
                  <td>
                    <span
                      v-if="id_edit_item == ''"
                      v-text="item.peso_inicial"
                    ></span>
                    <input
                      type="number"
                      class="form-control"
                      v-if="id_edit_item == item.id_especie"
                      name="aux_peso_inicial"
                      id="aux_peso_inicial"
                      v-model="aux_peso_inicial"
                    />
                  </td>
                  <td>
                    <button
                      v-if="!item.es_edita"
                      @click="removeItem(item.id_especie)"
                      class="btn btn-danger"
                    >
                      X
                    </button>
                    <button
                      v-if="
                        item.es_edita &&
                        id_edit_item == '' &&
                        item.cantidad == item.cant_actual
                      "
                      @click="editItem(item)"
                      class="btn btn-primary"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                    <button
                      v-if="item.es_edita && id_edit_item == item.id_especie"
                      @click="guardaEditItem(item.id)"
                      class="btn btn-success"
                    >
                      <i class="fas fa-check"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="modal-footer">
            <div class="form-group row">
              <div class="col-sm-12 text-right">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  Cancelar
                </button>
                <button
                  v-if="id == ''"
                  type="submit"
                  @click="guardar()"
                  class="btn btn-primary"
                >
                  Crear
                </button>
                <button
                  v-else
                  type="button"
                  @click="guardarEdita(form.id_contenedor)"
                  class="btn btn-primary"
                >
                  Actualizar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Form, HasError, AlertError } from "vform";

export default {
  name: "create-edit-stocking",
  props: ["id", "listado-contenedores", "listado-especies"],
  data() {
    return {
      form: new Form({
        id: "",
      }),
      listadoItems: [],
      nombresEspecies: [],
      newEspecie: "",
      newLote: "",
      newCantidad: "",
      newPeso: "",
      id_edit_item: "",
      phaseListing: {},
    };
  },
  methods: {
    editItem(especie) {
      this.id_edit_item = especie.id_especie;
      this.aux_lote = especie.lote;
      this.aux_cantidad = especie.cantidad;
      this.aux_peso_inicial = especie.peso_inicial;
    },
    removeItem(index) {
      let me = this;
      me.listadoItems.pop(index, 1);
      this.listadoEspecies.push({
        id: index,
        especie: this.nombresEspecies[index],
      });
    },
    nombreEspecie() {
      let me = this;
      axios.get("api/especies").then(function (response) {
        var auxEspecie = response.data;
        auxEspecie.forEach(
          (element) => (me.nombresEspecies[element.id] = element.especie)
        );
      });
    },
    guardar() {
      if (
        this.form.id_contenedor != "" &&
        this.form.nombre_siembra != "" &&
        this.form.fecha_inicio != "" &&
        this.listadoItems.length > 0
      ) {
        const data = {
          siembra: this.form,
          especies: this.listadoItems,
        };
        axios.post("api/siembras", data).then(({ response }) => {
          this.form.nombre_siembra = "";
          this.form.id_contenedor = "";
          this.form.fecha_inicio = "";
          this.newEspecie = "";
          this.newLote = "";
          this.newCantidad = "";
          this.newPeso = "";
          this.listadoItems = [];
          $("#modalSiembra").modal("hide");
        });
      } else {
        alert("Debe diligenciar todos los campos");
      }
    },
    anadirEspecie() {
      let me = this;
      if (
        this.newEspecie != "" &&
        this.newCantidad != "" &&
        this.newPeso != ""
      ) {
        me.listadoItems.push({
          id_especie: this.newEspecie,
          lote: this.newLote,
          cantidad: this.newCantidad,
          peso_inicial: this.newPeso,
        });

        const idEspecie = (element) => element.id == this.newEspecie;
        var index = this.listadoEspecies.findIndex(idEspecie);
        this.listadoEspecies.splice(index, 1);
        this.newEspecie = "";
        (this.newLote = ""), (this.newCantidad = "");
        this.newPeso = "";
      } else {
        alert("Debe diligenciar todos los campos");
      }
    },
    editarSiembra(siembra) {
      let me = this;
      me.form = siembra;
      axios
        .get("api/especies-siembra-edita/" + siembra.id)
        .then(function (response) {
          me.listadoItems = response.data.espxsiembra;
        });
    },
    guardarEdita() {
      let me = this;
      const data = {
        siembra: this.form,
        especies: this.listadoItems,
      };
      axios.post("api/anadir-especie-siembra", data).then(({ response }) => {
        this.form.nombre_siembra = "";
        this.form.id_contenedor = "";
        this.form.fecha_inicio = "";
        this.newEspecie = "";
        this.newLote = "";
        this.newCantidad = "";
        this.newPeso = "";
        this.listadoItems = [];
        this.listar();
        $("#modalSiembra").modal("hide");
      });
    },
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
    
    listPhases(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/phases?page=" + page, this.$root.config)
        .then(function (response) {
          me.phaseListing = response.data.phases;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },
  },
  mounted() {
    this.nombreEspecie();
    this.listPhases(1);
  },
};
</script>

<style>
</style>