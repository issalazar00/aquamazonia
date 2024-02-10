<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="foodCategoryModal" tabindex="-1" aria-labelledby="foodCategoryModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="foodCategoryModalLabel">Categoria</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row justify-content-center">
              <form>
                <div class="form-group">
                  <label for="name">Categoria</label>
                  <input type="text" class="form-control" id="name" placeholder=""
                    v-model="formFoodCategory.category" />
                </div>
                <div class="form-group">
                  <label for="name">Estado</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formFoodCategory.state" name="state"
                      id="true" value="1" checked>
                    <label class="form-check-label" for="true">
                      Activo
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formFoodCategory.state" name="state">
                    <label class="form-check-label" for="false">
                      Desactivado
                    </label>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" @click="closeModal()">
            Cerrar
          </button>
          <button type="button" class="btn btn-outline-primary" @click="SaveFoodCategory()">
            Guardar
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data() {
    return {
      edit: false,
      formFoodCategory: {
        category: "",
        state: true
      },
      formErrors : {}
    };
  },
  methods: {
    CreateFoodCategory() {
      let me = this;

      axios
        .post("api/food-categories", this.formFoodCategory, this.$root.config)
        .then(function (data) {
          $("#foodCategoryModal").modal("hide");
          me.formFoodCategory = {};
          me.$emit('list-food-categories');
        })
        .catch((response) => {
          console.log(response.response.data);
          this.formErrors = response.response.data.errors
        });
    },

    OpenEditFoodCategory(category) {
      let me = this;
      me.edit = true;
      $("#foodCategoryModal").modal("show");
      me.formFoodCategory = category;
    },

    SaveFoodCategory: function () {
      let me = this;
      if (this.edit == false) {
        this.CreateFoodCategory();
      } else {
        this.EditFoodCategory();
      }
    },

    EditFoodCategory() {
      let me = this;

      axios
        .put("api/food-categories/" + this.formFoodCategory.id, this.formFoodCategory, this.$root.config)
        .then(function () {
          $("#foodCategoryModal").modal("hide");
          me.formFoodCategory = {};

          me.$emit('list-food-categories');
        })
        .catch((response) => {
          console.log(response)
        });
    },

    ResetData() {
      let me = this;
      $("#foodCategoryModal").modal("hide");
      me.formFoodCategory = {};
    },

    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-food-categories");
    },

  },
  mounted() {
    console.log(this.formFoodCategory)
  },
};
</script>
