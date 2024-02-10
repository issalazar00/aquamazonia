<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="brandModal" tabindex="-1" aria-labelledby="brandModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="brandModalLabel">Fase</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row justify-content-center">
              <form>
                <div class="form-group">
                  <label for="name">Marca</label>
                  <input type="text" class="form-control" id="brand" placeholder="" v-model="formBrand.brand" />
                  <small class="form-text text-danger">{{ formErrors.brand }}</small>
                </div>
                <div class="form-group">
                  <label for="name">Estado</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formBrand.state" name="state" id="true"
                      value="1" checked>
                    <label class="form-check-label" for="true">
                      Activo
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formBrand.state" name="state" id="false"
                      value="0">
                    <label class="form-check-label" for="false">
                      Desactivado
                    </label>
                  </div>
                  <small class="form-text text-danger">{{ formErrors.state }}</small>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" @click="closeModal()">
            Cerrar
          </button>
          <button type="button" class="btn btn-outline-primary" @click="SaveBrand()">
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
      formBrand: {
        brand: 0,
      },
      formErrors: {
        errors: "",
      },
    };
  },
  methods: {
    CreateBrand() {
      let me = this;
      this.assignErrors(false);

      axios
        .post("api/brands", this.formBrand, this.$root.config)
        .then(function () {
          $("#brandModal").modal("hide");
          me.formBrand = {};
          me.$emit('list-brands');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },
    OpenEditBrand(brand) {
      let me = this;
      me.edit = true;
      $("#brandModal").modal("show");
      me.formBrand = brand;
    },
    SaveBrand: function () {
      let me = this;
      if (this.edit == false) {
        this.CreateBrand();
      } else {
        this.EditBrand();
      }
    },
    EditBrand() {
      let me = this;
      this.assignErrors(false);

      axios
        .put("api/brands/" + this.formBrand.id, this.formBrand, this.$root.config)
        .then(function () {
          $("#brandModal").modal("hide");
          me.formBrand = {};
          me.$emit('list-brands');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },

    ResetData() {
      let me = this;
      $("#brandModal").modal("hide");
      me.formBrand = {};
      this.assignErrors(false);
    },
    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-brands");
    },
    assignErrors(response) {
      if (response) {
        var errors = response.response.data.errors;
      } else { }
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
