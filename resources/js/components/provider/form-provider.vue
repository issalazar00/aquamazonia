<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="providerModal" tabindex="-1" aria-labelledby="providerModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="providerModalLabel">Fase</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row justify-content-center">
              <form>
                <div class="form-group">
                  <label for="name">Proveedor</label>
                  <input type="text" class="form-control" id="name" placeholder="" v-model="formProvider.provider" />
                  <small class="form-text text-danger">{{ formErrors.provider }}</small>
                </div>
                <div class="form-group">
                  <label for="nit">Nit</label>
                  <input type="text" class="form-control" id="nit" placeholder="" v-model="formProvider.nit" />
                  <small class="form-text text-danger">{{ formErrors.nit }}</small>
                </div>
                <div class="form-group">
                  <label for="name">Teléfono</label>
                  <input type="text" class="form-control" id="tel" placeholder="" v-model="formProvider.tel" />
                  <small class="form-text text-danger">{{ formErrors.tel }}</small>
                </div>
                <div class="form-group">
                  <label for="address">Dirección</label>
                  <input type="text" class="form-control" id="address" placeholder="" v-model="formProvider.address" />
                  <small class="form-text text-danger">{{ formErrors.address }}</small>
                </div>
                <div class="form-group">
                  <label for="name">Estado</label>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formProvider.state" name="state" id="true"
                      value="1" checked>
                    <label class="form-check-label" for="true">
                      Activo
                    </label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" v-model="formProvider.state" name="state" id="false"
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
          <button type="button" class="btn btn-outline-primary" @click="SaveProvider()">
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
      formProvider: {
        provider: "",
        nit: "",
        tel: "",
        address: "",
        state: 0
      },
      formErrors: {
        errors: "",
      },
    };
  },
  methods: {
    CreateProvider() {
      let me = this;
      this.assignErrors(false);

      axios
        .post("api/providers", this.formProvider, this.$root.config)
        .then(function () {
          $("#providerModal").modal("hide");
          me.formProvider = {};
          me.$emit('list-providers');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },

    OpenEditProvider(provider) {
      let me = this;
      me.edit = true;
      $("#providerModal").modal("show");
      me.formProvider = provider;
    },
    
    SaveProvider: function () {
      let me = this;
      if (this.edit == false) {
        this.CreateProvider();
      } else {
        this.EditProvider();
      }
    },

    EditProvider() {
      let me = this;
      this.assignErrors(false);

      axios
        .put("api/providers/" + this.formProvider.id, this.formProvider, this.$root.config)
        .then(function () {
          $("#providerModal").modal("hide");
          me.formProvider = {};

          me.$emit('list-providers');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },

    ResetData() {
      let me = this;
      $("#providerModal").modal("hide");
      me.formProvider = {};
      this.assignErrors(false);
    },
    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-providers");
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
