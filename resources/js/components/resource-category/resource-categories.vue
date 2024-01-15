<template>
  <div class="col-12 container">
    <h3 class="page-header">Tipo de recurso</h3>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#resourceCategoryModal"
          @click="$refs.FormResourceCategory.ResetData()">
          Crear Categoria
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Tipo de recurso</th>
              <th>Estado</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(resourceCategory, index) in resourceCategoryListing.data" :key="resourceCategory.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ resourceCategory.category }}</td>
              <td>{{ resourceCategory.state ? 'Active' : 'Inactivo' }}</td>
              <td>
                <button class="btn btn-outline-success" @click="ShowData(resourceCategory)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de impuestos -->
    <form-resource-category ref="FormResourceCategory" @list-resource-categories="listResourceCategories(1)" />
  </div>
</template>

<script>
import FormResourceCategory from "./form-resource-category.vue";
export default {
  components: { FormResourceCategory },
  data() {
    return {
      resourceCategoryListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listResourceCategories(1);
  },
  methods: {
    listResourceCategories(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/resource-categories?page=" + page, this.$root.config)
        .then(function (response) {
          me.resourceCategoryListing = response.data.resourceCategories;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (resourceCategory) {
      this.$refs.FormResourceCategory.OpenEditResourceCategory(resourceCategory);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormResourceCategory.ResetData();
      me.listResourceCategories(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/resource-categories/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listResourceCategories(1);
        });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
