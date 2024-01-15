<template>
  <div class="col-12 container">
    <h3 class="page-header">Bodega</h3>
    <section v-if="isLoading"> Cargando... </section>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#warehouseModal"
          @click="$refs.FormWarehouse.ResetData()">
          Crear Bodega
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Bodega</th>
              <th>Descripción</th>
              <th>Estado</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody v-if="warehouseListing.data">
            <tr v-for="(warehouse, index) in warehouseListing.data" :key="warehouse.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ warehouse.warehouse }}</td>
              <td>{{ warehouse.description }}</td>
              <td>{{ warehouse.state ? 'Activo' : 'Inactivo' }}</td>
              <td>
                <button class="btn btn-outline-success" @click="ShowData(warehouse)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td>No hay datos registrados recientemente</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de bodegas -->
    <form-warehouse ref="FormWarehouse" @list-warehouses="listWarehouse(1)" />
  </div>
</template>

<script>
import FormWarehouse from "./form-warehouse.vue";
export default {
  components: { FormWarehouse },
  data() {
    return {
      warehouseListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listWarehouse(1);
  },
  methods: {
    listWarehouse(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/warehouses?page=" + page, this.$root.config)
        .then(function (response) {
          me.warehouseListing = response.data.warehouses;
        })
        .catch(e => {
          console.log(e)
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (warehouse) {
      this.$refs.FormWarehouse.OpenEditWarehouse(warehouse);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormWarehouse.ResetData();
      me.listWarehouse(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/warehouses/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listWarehouse(1);
        });
    },
  },
  mounted() {
  },
};
</script>
