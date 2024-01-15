<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="resourceCategoryModal" tabindex="-1" aria-labelledby="resourceCategoryModalLabel"
    aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resourceCategoryModalLabel">Categoria</h5>
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
                    v-model="formResourceCategory.category" />
                </div>
                <div class="form-group">
                  <label for="name">Estado</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formResourceCategory.state" name="state"
                      id="true" value="1" checked>
                    <label class="form-check-label" for="true">
                      Activo
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formResourceCategory.state" name="state">
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
          <button type="button" class="btn btn-outline-primary" @click="SaveResourceCategory()">
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
      formResourceCategory: {
        category: "",
        state: true
      },
      formErrors : {}
    };
  },
  methods: {
    CreateResourceCategory() {
      let me = this;

      axios
        .post("api/resource-categories", this.formResourceCategory, this.$root.config)
        .then(function (data) {
          $("#resourceCategoryModal").modal("hide");
          me.formResourceCategory = {};
          me.$emit('list-resource-categories');
        })
        .catch((response) => {
          console.log(response.response.data);
          this.formErrors = response.response.data.errors
        });
    },

    OpenEditResourceCategory(category) {
      let me = this;
      me.edit = true;
      $("#resourceCategoryModal").modal("show");
      me.formResourceCategory = category;
    },

    SaveResourceCategory: function () {
      let me = this;
      if (this.edit == false) {
        this.CreateResourceCategory();
      } else {
        this.EditResourceCategory();
      }
    },

    EditResourceCategory() {
      let me = this;

      axios
        .put("api/resource-categories/" + this.formResourceCategory.id, this.formResourceCategory, this.$root.config)
        .then(function () {
          $("#resourceCategoryModal").modal("hide");
          me.formResourceCategory = {};

          me.$emit('list-resource-categories');
        })
        .catch((response) => {
          console.log(response)
        });
    },

    ResetData() {
      let me = this;
      $("#resourceCategoryModal").modal("hide");
      me.formResourceCategory = {};
    },

    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-resource-categories");
    },

  },
  mounted() {
    console.log(this.formResourceCategory)
  },
};
</script>
