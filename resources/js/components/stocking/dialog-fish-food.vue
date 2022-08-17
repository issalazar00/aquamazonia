<template>
  <!-- Modal añadir alimentos a siembras -->
  <div
    class="modal fade"
    id="modalRecursos"
    tabindex="-1"
    role="dialog"
    aria-labelledby="exampleModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title" id="exampleModalLabel">
            Alimentos por siembra
          </h3>
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
          <form class="row" id="editarAlimentacion">
            <!-- <div class="col-md-6"> -->
            <div class="form-group col-md-3">
              <label for="fecha_registro_alimentacion" class=""
                >Fecha (*)</label
              >
              <input
                type="date"
                class="form-control"
                id="fecha_registro_alimentacion"
                aria-describedby="fecha_registro_alimentacion"
                placeholder="fecha_registro_alimentacion"
                v-model="form.fecha_ra"
                required
              />
            </div>

            <div class="form-group col-md-3">
              <label for="alimento" class="">Alimento (*)</label>
              <select
                class="form-control custom-select"
                id="alimento"
                v-model="form.id_alimento"
                required
              >
                <option>--Seleccionar--</option>
                <option
                  v-for="(alimento, index) in listadoAlimentos"
                  :key="index"
                  v-bind:value="alimento.id"
                >
                  {{ alimento.alimento }}
                </option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="detalles" class="">Detalles</label>
              <textarea
                class="form-control"
                id="detalles"
                aria-describedby="detalles"
                placeholder="Detalles"
                v-model="form.detalles"
              ></textarea>
            </div>

            <div class="form-group col-md-3">
              <label for="minutos hombre" class="">Minutos hombre</label>
              <input
                type="number"
                class="form-control"
                step="any"
                id="minutos_hombre"
                aria-describedby="minutos_hombre"
                placeholder="Minutos hombre"
                v-model="form.minutos_hombre"
              />
            </div>

            <div class="form-group col-md-3">
              <label for="cant_manana" class="">Kg Mañana</label>
              <input
                type="number"
                step="any"
                class="form-control"
                id="kg_manana"
                aria-describedby="cant_manana"
                placeholder="Kg Mañana"
                v-model="form.cant_manana"
              />
            </div>
            <div class="form-group col-md-3">
              <label for="cant_tarde" class="">Kg tarde</label>
              <input
                type="number"
                step="any"
                class="form-control"
                id="cant_tarde"
                aria-describedby="cant_tarde"
                placeholder="Kg tarde"
                v-model="form.cant_tarde"
              />
            </div>
            <div class="form-group col-md-4">
              <label for="conv_alimenticia"
                >Conversión alimenticia teórica</label
              >
              <input
                type="number"
                step="any"
                class="form-control"
                id="conv_alimenticia"
                placeholder="Conversión alimenticia teórica"
                v-model="form.conv_alimenticia"
              />
            </div>
            <div class="modal-footer">
              <button
                type="button"
                class="btn btn-primary"
                @click="guardarRecursos()"
              >
                <span
                  v-text="editandoAlimento == 0 ? 'Guardar' : 'Actualizar'"
                ></span>
              </button>
            </div>
          </form>

          <div class="container">
            <table
              class="table table-sm table-hover table-responsive table-bordered"
            >
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
                <tr
                  v-for="(item, index) in listadoAlimentosSiembra"
                  :key="index"
                >
                  <td v-text="index + 1"></td>
                  <td v-text="item.actividad"></td>
                  <td v-text="item.fecha_ra"></td>
                  <td>{{ item.alimento }}</td>
                  <td
                    v-text="
                      item.cant_manana == null ? '-' : item.cant_manana + ' kg'
                    "
                  ></td>
                  <td
                    v-text="
                      item.cant_tarde == null ? '-' : item.cant_tarde + ' kg'
                    "
                  ></td>
                  <td v-text="item.detalles"></td>
                  <td>
                    <button
                      class="btn btn-success"
                      @click="editarAlimento(item)"
                    >
                      <i class="fas fa-edit"></i>
                    </button>
                  </td>
                  <td>
                    <a
                      class="btn btn-danger"
                      @click="eliminarAlimento(item.id_registro)"
                    >
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
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import { Form, HasError, AlertError } from "vform";
export default {
  props: ["siembra_id"],
  data() {
    return {
      editandoAlimento: 0,
      form: new Form({
        tipo_actividad: 1,
        fecha_ra: "",
        id_alimento: "",
        id_registro: "",
        detalles: "",
        minutos_hombre: "",
        cant_manana: "",
        cant_tarde: "",
        conv_alimenticia: "",
        id_siembra: "",
      }),
      listadoAlimentos: [],
      listadoAlimentosSiembra: "",
    };
  },
  methods: {
    eliminarAlimento(objeto) {
      let me = this;
      Swal.fire({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          axios.delete("api/recursos-necesarios/" + objeto).then(({ data }) => {
            me.listarRecursosAlimentos(this.siembra_id);
          });
        }
      });
    },
    guardarRecursos() {
      let me = this;
      if (this.editandoAlimento == 0) {
        this.form.id_siembra = this.siembra_id;
        axios.post("api/recursos-necesarios", this.form).then(({ data }) => {
          
          Swal.fire(
            "Excelente!",
            "Los datos se guardaron correctamente!",
            "success"
          );

          me.listarRecursosAlimentos(this.siembra_id);
        });
      } else {
        this.form
          .put("api/recursos-necesarios/" + this.form.id_registro)
          .then(({ data }) => {
            this.form.reset();
            this.editandoAlimento = 0;
            me.listarRecursosAlimentos(this.siembra_id);
          });
      }
    },
    listarRecursosAlimentos(id) {
      let me = this;
      axios.post(`api/siembras-alimentacion/${id}`).then(function (response) {
        me.listadoAlimentosSiembra = response.data.recursosNecesarios;
      });
    },
    editarAlimento(objeto) {
      this.editandoAlimento = 1;
      this.form.fill(objeto);
    },
    listarAlimentos() {
      let me = this;
      axios.get("api/alimentos").then(function (response) {
        me.listadoAlimentos = response.data;
      });
    },
  },
  mounted() {
    this.listarAlimentos();
  },
};
</script>