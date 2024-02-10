<template>
    <div
    class="modal fade"
    id="modalCostos"
    tabindex="-1"
    role="dialog"
    aria-labelledby="modalCostosLabel"
    aria-hidden="true"
    data-backdrop="static"
  >
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCostosLabel">
            Historial de costos
          </h5>
          <button
            type="button"
            class="close"
            @click="cerrarCostos()"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="col-12" v-if="listadoCostos.length > 0">
            <downloadexcel
              class="btn btn-success float-right mb-1"
              :fetch="fetchData"
              :fields="json_fields"
              name="historial-costos-alimentos.xls"
              type="xls"
            >
              <i class="fa fa-fw fa-download"></i> Generar Excel
            </downloadexcel>
          </div>
          <table class="table table-sm table-bordered table-hover">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Fecha registro</th>
                <th scope="col">Alimento</th>
                <th scope="col">Costo</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(registro, index) in listadoCostos"
                :key="registro.id"
              >
                <th scope="row" v-text="index + 1"></th>
                <td>{{ registro.fecha_registro }}</td>
                <td class="text-right">{{ registro.alimento }}</td>
                <td class="text-right">$ {{ registro.costo }}</td>
                <td>
                  <button
                    @click="eliminarHistorial(registro.id)"
                    class="btn btn-danger"
                    type="button"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            @click="cerrarCostos()"
          >
            Cerrar
          </button>
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
      listadoCostos: [],

      json_fields: {
        "Fecha Registro": "fecha_registro",
        Alimento: "alimento",
        Costo: "costo",
      },
    }
  },
  components: {
    downloadexcel,
  },
  methods: {
    async fetchData() {
      let me = this;
      const response = await this.listadoCostos;
      return this.listadoCostos;
    },
    cerrarCostos() {
      $("#modalCostos").modal("hide");
      this.listadoCostos = [];
    },
    eliminarHistorial(index) {
      let me = this;
      Swal.fire({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c7120c",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Aceptar!",
        reverseButtons: true,
        dangerMode: true,
      }).then((result) => {
        if (result.isConfirmed) {
          me.form
            .delete("api/historial-alimentos-costos/" + index)
            .then(({ data }) => {
              me.listar("");
              me.verCostos();
            });
        }
      });
    },

    ShowCosts(idAlimento) {
      let me = this;
      $("#modalCostos").modal("show");
      axios
        .get("api/historial-alimentos-costos?idAlimento=" + idAlimento)
        .then(function (response) {
          me.listadoCostos = response.data;
        });
    },
  }
}
</script>