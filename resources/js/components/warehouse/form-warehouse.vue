<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="warehouseModal" tabindex="-1" aria-labelledby="warehouseModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="warehouseModalLabel">Bodega</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row justify-content-center">
              <form>
                <div class="form-group">
                  <label for="name">Bodega</label>
                  <input type="text" class="form-control" id="warehouse" placeholder="" v-model="formWarehouse.warehouse" />
                  <small class="form-text text-danger">{{ formErrors.warehouse }}</small>
                </div>
                <div class="form-group">
                  <label for="name">Descripción</label>
                  <input type="text" class="form-control" id="description" placeholder="description" v-model="formWarehouse.description" />
                  <small class="form-text text-danger">{{ formErrors.description }}</small>
                </div>
                <div class="form-group">
                  <label for="name">Estado</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formWarehouse.state" name="state" id="true" value="1" checked>
                    <label class="form-check-label" for="true">
                      Activo
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formWarehouse.state" name="state" id="false" value="0">
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
          <button type="button" class="btn btn-outline-primary" @click="SaveWarehouse()">
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
      formWarehouse: {
        warehouse: "",
        description:"",
        state: true
      },
      formErrors: {
        errors: "",
      },
    };
  },
  methods: {
    CreateWarehouse() {
      let me = this;
      this.assignErrors(false);

      axios
        .post("api/warehouses", this.formWarehouse, this.$root.config)
        .then(function () {
          $("#warehouseModal").modal("hide");
          me.formWarehouse = {};
          me.$emit('list-warehouses');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },
    OpenEditWarehouse(warehouse) {
      let me = this;
      me.edit = true;
      $("#warehouseModal").modal("show");
      me.formWarehouse = warehouse;
    },
    SaveWarehouse: function () {
      let me = this;
      if (this.edit == false) {
        this.CreateWarehouse();
      } else {
        this.EditWarehouse();
      }
    },


    EditWarehouse() {
      let me = this;
      this.assignErrors(false);

      axios
        .put("api/warehouses/" + this.formWarehouse.id, this.formWarehouse, this.$root.config)
        .then(function () {
          $("#warehouseModal").modal("hide");
          me.formWarehouse = {};

          me.$emit('list-warehouses');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },

    ResetData() {
      let me = this;
      $("#warehouseModal").modal("hide");
      me.formWarehouse = {};
      this.assignErrors(false);
    },
    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-warehouses");
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
