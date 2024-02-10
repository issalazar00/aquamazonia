<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Gestión de recursos</div>

          <div class="card-body">
            <div class="row mb-1">
              <div class="col-12 text-right">
                <button class="btn btn-success" @click="$refs.FormResource.ResetData()" data-toggle="modal"
                  data-target="#modalResources">
                  Añadir Recurso
                </button>
              </div>
            </div>
            <div class="row">
              <table class="table table-hover table-sm table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Recurso</th>
                    <th scope="col">Unidad</th>
                    <th scope="col">Costo</th>
                    <th>Bodega</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th scope="col">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(recurso, index) in listado" :key="index">
                    <th scope="row" v-text="index + 1"></th>
                    <td v-text="recurso.recurso"></td>
                    <td v-text="recurso.unidad"></td>
                    <td v-text="recurso.costo"></td>
                    <td>{{ recurso.warehouse ? recurso.warehouse.warehouse : "" }}</td>
                    <td>{{ recurso.brand ? recurso.brand.brand : "" }}</td>
                    <td>{{ recurso.category ? recurso.category.category : "" }}</td>
                    <td>{{ recurso.provider ? recurso.provider.provider : "" }}</td>
                    
                    <td>
                      <button @click="ShowData(recurso)" class="btn btn-success" type="button">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button @click="Delete(recurso.id)" class="btn btn-danger" type="button">
                        <i class="fas fa-trash"></i>
                      </button>
                      <button class="btn btn-primary" type="button" @click="ShowCosts(recurso.id)">
                        <i class="fas fa-dollar-sign"></i>
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
    <form-resource ref="FormResource" @list-resources="listResources"></form-resource>
    <resource-history ref="ResourceHistory"></resource-history>
  </div>
</template>

<script>
import FormResource from "./form-resource.vue";
import ResourceHistory from "./resource-history.vue";

export default {
  data() {
    return {
      listado: [],
    };
  },
  components: {
    FormResource,
    ResourceHistory
  },
  methods: {
    listResources() {
      let me = this;
      axios.get("api/recursos").then(function (response) {
        me.listado = response.data;
      });
    },
    ShowData(resource) {
      this.$refs.FormResource.OpenEditResource(resource);
    },

    Delete(resourceId) {
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
          me.form.delete("api/recursos/" + resourceId).then(({ data }) => {
            me.listResources();
          });
        }
      });
    },

    ShowCosts(idRecurso) {
      this.$refs.ResourceHistory.ShowCosts(idRecurso);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormResource.ResetData();
      me.listResources(1);
    },

  },
  mounted() {
    this.listResources();
  },
};
</script>
