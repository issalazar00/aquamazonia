<template>
  <div class="col-12 container">
    <h3 class="page-header">Tipo de Alimento</h3>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#foodCategoryModal"
          @click="$refs.FormFoodCategory.ResetData()">
          Crear Categoria
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Tipo de Alimento</th>
              <th>Estado</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(foodCategory, index) in foodCategoryListing.data" :key="foodCategory.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ foodCategory.category }}</td>
              <td>{{ foodCategory.state ? 'Active' : 'Inactivo' }}</td>
              <td>
                <button class="btn btn-outline-success" @click="ShowData(foodCategory)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de impuestos -->
    <form-food-category ref="FormFoodCategory" @list-food-categories="listFoodCategories(1)" />
  </div>
</template>

<script>
import FormFoodCategory from "./form-food-category.vue";
export default {
  components: { FormFoodCategory },
  data() {
    return {
      foodCategoryListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listFoodCategories(1);
  },
  methods: {
    listFoodCategories(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/food-categories?page=" + page, this.$root.config)
        .then(function (response) {
          me.foodCategoryListing = response.data.foodCategories;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (foodCategory) {
      this.$refs.FormFoodCategory.OpenEditFoodCategory(foodCategory);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormFoodCategory.ResetData();
      me.listFoodCategories(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/food-categories/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listFoodCategories(1);
        });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
