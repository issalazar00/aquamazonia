<template>
  <div class="col-12 container">
    <h3 class="page-header">Proveedor</h3>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#providerModal"
          @click="$refs.FormProvider.ResetData()">
          Crear Proveedor
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Proveedor</th>
              <th>NIT</th>
              <th>Teléfono</th>
              <th>Dirección</th>
              <th>Estado</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(provider, index) in providerListing.data" :key="provider.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ provider.provider }}</td>
              <td>{{ provider.nit }}</td>
              <td>{{ provider.tel }}</td>
              <td>{{ provider.address }}</td>
              <td>{{ provider.state ? 'Activo' : 'Inactivo' }}</td>

              <td>
                <button class="btn btn-outline-success" @click="ShowData(provider)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de proveedores -->
    <form-provider ref="FormProvider" @list-providers="listProviders(1)" />
  </div>
</template>

<script>
import FormProvider from "./form-provider.vue";
export default {
  components: { FormProvider },
  data() {
    return {
      providerListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listProviders(1);
  },
  methods: {
    listProviders(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/providers?page=" + page, this.$root.config)
        .then(function (response) {
          me.providerListing = response.data.providers;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (provider) {
      this.$refs.FormProvider.OpenEditProvider(provider);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormProvider.ResetData();
      me.listProviders(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/providers/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listProviders(1);
        });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
