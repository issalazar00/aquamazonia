<template>
  <div class="col-12 container">
    <h3 class="page-header">Marcas</h3>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#brandModal"
          @click="$refs.FormBrand.ResetData()">
          Crear Marca
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Marca</th>
              <th>Estado</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(brand, index) in brandListing.data" :key="brand.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ brand.brand }}</td>
              <td>{{ brand.state ? 'Activo' : 'Inactivo' }}</td>
              <td>
                <button class="btn btn-outline-success" @click="ShowData(brand)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de impuestos -->
    <form-brand ref="FormBrand" @list-brands="listBrands(1)" />
  </div>
</template>

<script>
import FormBrand from "./form-brand.vue";
export default {
  components: { FormBrand },
  data() {
    return {
      brandListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listBrands(1);
  },
  methods: {
    listBrands(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/brands?page=" + page, this.$root.config)
        .then(function (response) {
          me.brandListing = response.data.brands;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (brand) {
      this.$refs.FormBrand.OpenEditBrand(brand);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormBrand.ResetData();
      me.listBrands(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/brands/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listBrands(1);
        });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
