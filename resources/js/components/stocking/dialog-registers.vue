<template>
  <!-- Modal registros -->
  <div class="modal" tabindex="-1" role="dialog" id="modalIngreso">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-center col-md-9">Registros</h5>
          <button type="button" class="btn btn-primary" @click="
            ver_registros == 1 ? (ver_registros = 0) : (ver_registros = 1)
          ">
            <span v-if="ver_registros == 1">Crear Registros <i class="fas fa-arrow-right"></i></span>
            <span v-if="ver_registros == 0"><i class="fas fa-arrow-left"></i> Ver listado de registros</span>
          </button>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div id="mostrarRegistros" v-if="ver_registros == 1">
            <div class="col-md-12">
              <h5>Filtrar por:</h5>
              <form class="row">
                <div class="form-group col-md-4">
                  <label for="tipo_registro">Tipo</label>
                  <select class="form-control" id="tipo_registro" v-model="f_actividad">
                    <option value="3">Peso inicial</option>
                    <option value="0">Muestreo</option>
                    <option value="1">Pesca</option>
                    <option value="2">Mortalidad inicial</option>
                  </select>
                </div>
                <div class="form-group col-md-3">
                  <label for="search">Desde: </label>
                  <input class="form-control" type="date" placeholder="Search" aria-label="fecha_desde"
                    v-model="f_fecha_d" />
                </div>
                <div class="form-group col-md-3">
                  <label for="search">Hasta: </label>
                  <input class="form-control" type="date" placeholder="Search" aria-label="fecha_hasta"
                    v-model="f_fecha_h" />
                </div>
                <div class="form-group col-md-2">
                  <button class="btn btn-primary rounded-circle mt-4" @click="filtrarIngresos()" type="button">
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </form>
            </div>
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Especie</th>
                  <th>Tipo de registro</th>
                  <th>Fecha</th>
                  <th>Peso ganado (gr)</th>
                  <th>Mortalidad</th>
                  <th>Biomasa(kg)</th>
                  <th>Cantidad</th>
                  <th>Eliminar</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(registro, index) in listadoRegistros" :key="registro.id">
                  <th v-text="index + 1"></th>
                  <td v-text="registro.especie"></td>
                  <td v-text="tipoRegistro[registro.tipo_registro]"></td>
                  <td v-text="registro.fecha_registro"></td>
                  <td v-text="
                    registro.peso_ganado == null
                      ? '-'
                      : registro.peso_ganado + 'gr'
                  "></td>
                  <td v-text="
                    registro.mortalidad == null ? '-' : registro.mortalidad
                  "></td>
                  <td v-if="registro.biomasa != null">
                    {{ registro.biomasa.toFixed(2) }}
                  </td>
                  <td v-else>-</td>
                  <td v-if="registro.cantidad != null">
                    {{ Math.floor(registro.cantidad) }}
                  </td>
                  <td v-else>-</td>
                  <td v-if="registro.tipo_registro != 3">
                    <button class="btn btn-danger" @click="eliminarRegistro(registro.id, registro)">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div id="crearRegistros" v-if="ver_registros == 0">
            <div class="row">
              <div class="form-group col-sm-6 col-lg-3">
                <label for="fecha_registro">Fecha Registro</label>
                <input type="date" class="form-control" id="fecha_registro" placeholder="Fecha"
                  v-model="fecha_registro" />
              </div>
              <div class="form-group col-sm-6 col-lg-3">
                <label for="tipo_registro">Tipo</label>
                <select class="form-control" id="tipo_registro" v-model="tipo_registro">
                  <option value="0">Muestreo</option>
                  <option value="1">Pesca</option>
                  <option value="2">Mortalidad inicial</option>
                  <option value="3">Peso inicial</option>
                </select>
              </div>
            </div>
            <div class="col-sm-12 col-lg-8 mx-auto">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th scope="col">Especie</th>
                    <th scope="col" v-if="tipo_registro == 0">
                      Peso actual (gr)
                    </th>
                    <th scope="col" v-if="tipo_registro == 0">Mortalidad</th>
                    <th scope="col" v-if="tipo_registro == 1">Biomasa (kg)</th>
                    <th scope="col" v-if="tipo_registro == 2">
                      Mortalidad Inicial
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pez in pecesxSiembra" :key="pez.id">
                    <th scope="row" v-text="pez.especie"></th>
                    <td v-if="tipo_registro == 0">
                      <input type="number" class="form-control" v-bind:required="tipo_registro == 0 ? 'required' : ''"
                        step="any" v-model="campos[pez.id_siembra][pez.id]['peso_ganado']" />
                    </td>
                    <td v-if="tipo_registro == 0 || tipo_registro == 2">
                      <input type="number" step="any" id="mortalidad" value="" class="form-control" v-bind:required="
                        tipo_registro == 0 || 2 ? 'required' : ''
                      " v-model="campos[pez.id_siembra][pez.id]['mortalidad']" />
                    </td>
                    <td v-if="tipo_registro == 1">
                      <input type="number" step="any" class="form-control"
                        v-bind:required="tipo_registro == 1 ? 'required' : ''"
                        v-model="campos[pez.id_siembra][pez.id]['biomasa']" />
                    </td>
                    <!-- <td v-if="tipo_registro == 1 ">
                        <input type="number" class="form-control" v-bind:required="tipo_registro == 1 ? 'required' : ''" v-model="campos[pez.id_siembra][pez.id]['cantidad']">
                      </td>                                                  -->
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Cerrar
          </button>
          <button type="button" class="btn btn-primary" v-if="ver_registros == 0" @click="crearRegistro(siembra_id)">
            Crear registro
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Swal from 'sweetalert2';

export default {
  props: ['siembra_id'],
  data() {
    return {
      listadoRegistros: [],
      pecesxSiembra: [],
      tipoRegistro: [],

      // Registros
      id_siembra: "",
      id_especie: "",
      fecha_registro: "",
      tipo_registro: "",
      peso_ganado: "",
      mortalidad: 0,
      biomasa: "",
      cantidad: "",
      aux_lote: "",
      aux_cantidad: "",
      aux_peso_inicial: "",
      ver_registros: 1,
      campos: {},

      // Filtros registros
      f_siembra: "",
      f_actividad: "",
      f_fecha_d: "",
      f_fecha_h: "",
    };
  },
  methods: {
    listarRegistros(id) {
      this.tipo_registro = 0;

      this.ver_registros = 1;
      let me = this;
      axios.post("api/registros-siembra/" + id).then(function (response) {
        me.listadoRegistros = response.data;
      });

      this.especiesSiembra(id);
      this.listarCampos(id)
    },
    especiesSiembra(idSiembra) {
      let me = this;
      axios
        .get("api/especies-siembra?idSiembra=" + idSiembra)
        .then(function (response) {
          me.pecesxSiembra = response.data;
        });
    },
    listarCampos(siembra_id) {
      let me = this
      const data = {
        'siembra_id': siembra_id
      }
      axios.get("api/siembras/campos", { params: data }).then(function (response) {
        me.campos = response.data;
      });
    },
    crearRegistro(id) {
      let me = this;
      this.ver_registros = 0;
      let aux_campos = me.campos[id];

      const data = {
        campos: aux_campos,
        id_siembra: id,
        fecha_registro: this.fecha_registro,
        tipo_registro: this.tipo_registro,
      };
      axios.post("api/registros", data).then(({ response }) => {
        me.aux_campos = [];
        me.ver_registros = 1;
        me.listarRegistros(id);
      });
    },

    filtrarIngresos() {
      let me = this;

      // if(this.f_siembra == ''){this.smb = '-1'}else{this.smb = this.f_siembra}
      if (this.f_actividad == "") {
        this.act = "-1";
      } else {
        this.act = this.f_actividad;
      }
      if (this.f_fecha_d == "") {
        this.f_d = "-1";
      } else {
        this.f_d = this.f_fecha_d;
      }
      if (this.f_fecha_h == "") {
        this.f_h = "-1";
      } else {
        this.f_h = this.f_fecha_h;
      }

      const data = {
        f_siembra: this.siembra_id,
        f_actividad: this.act,
        f_fecha_d: this.f_d,
        f_fecha_h: this.f_h,
      };
      axios.post("api/filtro-registros", data).then((response) => {
        me.listadoRegistros = response.data;
      });
    },
    eliminarRegistro(id, objeto) {
      let me = this;
      Swal.fire({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        buttons: ["Cancelar", "Aceptar"],
        dangerMode: true,
      }).then((willDelete) => {
        if (willDelete) {
          const data = {
            campos: objeto,
          };
          axios.put("api/registros/" + id, data).then(({ data }) => {
            me.listarRegistros(this.siembra_id);

          });
        }
      });
    },
  },
  mounted() {
    this.tipoRegistro[0] = "Muestreo";
    this.tipoRegistro[1] = "Pesca";
    this.tipoRegistro[2] = "Mortalidad Inicial";
  }
};
</script>