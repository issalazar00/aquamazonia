<template>
  <div class="col-12 container">
    <h3 class="page-header">Fases</h3>
    <section v-if="!isLoading">
      <div class="row justify-content-end mx-4">
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#phaseModal"
          @click="$refs.FormPhase.ResetData()">
          Crear Fase
        </button>
      </div>
      <div class="card-body table-responsive-sm">
        <table class="table table-sm table-bordered">
          <thead class="thead-primary">
            <tr>
              <th scope="col">#</th>
              <th>Fase</th>
              <th>Opciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(phase, index) in phaseListing.data" :key="phase.id">
              <td scope="row">{{ index + 1 }}</td>
              <td>{{ phase.phase }}</td>
              <td>
                <button class="btn btn-outline-success" @click="ShowData(phase)">
                  Editar
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
    <!-- Modal para creacion y edicion de impuestos -->
    <form-phase ref="FormPhase" @list-phases="listPhases(1)" />
  </div>
</template>

<script>
import FormPhase from "./form-phase.vue";
export default {
  components: { FormPhase },
  data() {
    return {
      phaseListing: {},
      isLoading: false,
    };
  },
  created() {
    this.listPhases(1);
  },
  methods: {
    listPhases(page = 1) {
      let me = this;
      me.isLoading = true;
      axios
        .get("api/phases?page=" + page, this.$root.config)
        .then(function (response) {
          me.phaseListing = response.data.phases;
        })
        .finally(() => {
          me.isLoading = false;
        });
    },

    ShowData: function (phase) {
      this.$refs.FormPhase.OpenEditPhase(phase);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormPhase.ResetData();
      me.listPhases(1);
    },
    changeState: function (id) {
      let me = this;
      axios
        .post("api/phases/" + id + "/activate", null, me.$root.config)
        .then(function () {
          me.listPhases(1);
        });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
};
</script>
