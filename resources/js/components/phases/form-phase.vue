<template>
  <!-- Modal para creacion y edicion de impuestos -->
  <div class="modal fade" id="phaseModal" tabindex="-1" aria-labelledby="phaseModalLabel" aria-hidden="true"
    data-backdrop="static">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="phaseModalLabel">Fase</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="container">
            <div class="row justify-content-center">
              <form>
                <div class="form-group">
                  <label for="name">Fase</label>
                  <input type="text" class="form-control" id="name" placeholder="" v-model="formPhase.phase" />
                  <small class="form-text text-danger">{{ formErrors.phase }}</small>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" @click="closeModal()">
            Cerrar
          </button>
          <button type="button" class="btn btn-outline-primary" @click="SavePhase()">
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
      formPhase: {
        phase: 0,
      },
      formErrors: {
        errors: "",
      },
    };
  },
  methods: {
    CreatePhase() {
      let me = this;
      this.assignErrors(false);

      axios
        .post("api/phases", this.formPhase, this.$root.config)
        .then(function () {
          $("#phaseModal").modal("hide");
          me.formPhase = {};
          me.$emit('list-phases');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },
    OpenEditPhase(phase) {
      let me = this;
      me.edit = true;
      $("#phaseModal").modal("show");
      me.formPhase = phase;
    },
    SavePhase: function () {
      let me = this;
      if (this.edit == false) {
        this.CreatePhase();
      } else {
        this.EditPhase();
      }
    },


    EditPhase() {
      let me = this;
      this.assignErrors(false);

      axios
        .put("api/phases/" + this.formPhase.id, this.formPhase, this.$root.config)
        .then(function () {
          $("#phaseModal").modal("hide");
          me.formPhase = {};

          me.$emit('list-phases');
        })
        .catch((response) => {
          this.assignErrors(response);
        });
    },

    ResetData() {
      let me = this;
      $("#phaseModal").modal("hide");
      me.formPhase = {};
      this.assignErrors(false);
    },
    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-phases");
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
