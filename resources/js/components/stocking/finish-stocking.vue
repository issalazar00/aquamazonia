<template>
  <!-- Modal Finalizar -->
  <div class="modal fade" id="modalFinalizar" tabindex="-1" role="dialog" aria-labelledby="modalFinalizarLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Finalizar siembra</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form method="POST">
            <div class="row">
              <div class="col">
                <h6>Inicio Descanso</h6>
                <input type="date" class="form-control" placeholder="First name" v-model="ini_descanso" required>
              </div>
              <div class="col">
                <h6>Fin descanso</h6>
                <input type="date" class="form-control" placeholder="Last name" v-model="fin_descanso">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" class="btn btn-primary" @click="fechaDescanso()">Guardar</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  props: ['id'],
  data() {
    return {
      // Finalización de siembra
      ini_descanso: '',
      fin_descanso: '',
    }
  },
  methods: {
    fechaDescanso() {
      let me = this;
      if (this.ini_descanso != '') {
        if (this.fin_descanso != '') {
          const data = {
            'id': this.id,
            'ini_descanso': this.ini_descanso,
            'fin_descanso': this.fin_descanso
          }
          axios.post('api/actualizarEstado/' + this.id, data)
            .then(({ response }) => {
              this.ini_descanso = '';
              this.fin_descanso = '';
              $('#modalFinalizar').modal('hide');
             this.$emit('list-stocking')
            });
        } else {
          const data = {
            'id': this.id,
            'ini_descanso': this.ini_descanso,
          }

          axios.post('api/actualizarEstado/' + this.id, data)
            .then(({ response }) => {
              this.ini_descanso = '';
              $('#modalFinalizar').modal('hide');
             this.$emit('list-stocking')
            });
        }
      } else {
        swal("Advertencia", "Por favor, diligencia los datos restantes", "warning");
      }
    },
  }
}
</script>
