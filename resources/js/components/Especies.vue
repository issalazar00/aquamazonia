<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Gestión de especies</div>

                    <div class="card-body">
                        <div class="row mb-1">
                            <div class="col-12 text-right ">
                              <button class="btn btn-success" @click="abrirCrear()">Añadir especie</button>
                            </div>
                        </div>
                        <div class="row">
                            <table class="table table-striped table-sm table-bordered">
                              <thead class="thead-primary">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Especie</th>
                                  <th scope="col">Descripción</th>
                                  <th scope="col">Estado</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr v-for="(especie, index) in listado" :key="especie.id">
                                  <th scope="row" v-text="index"></th>
                                  <td v-text="especie.especie"></td>
                                  <td v-text="especie.descripcion"></td>
                                  <td>
                                    <button @click="cargaEditar(especie)" class="btn btn-success" type="button">
                                      <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="eliminar(especie.id)" class="btn btn-danger" type="button">
                                      <i class="fas fa-trash"></i>
                                    </button>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalEspecies" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modalEspeciesLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalEspeciesLabel" v-text="editando ==0 ? 'Crear especie' : 'Actualizar especie'"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form @submit.prevent="editando == 0 ? guardar() : editar()">
                  <div class="form-group row">
                    <label for="contenedor" class="col-sm-12 col-md-4 col-form-label">Especie</label>
                    <div class="col-sm-12 col-md-8">
                      <input type="text" class="form-control" id="especie"  :class="{ 'is-invalid': form.errors.has('especie') }" v-model="form.especie">
                       <has-error :form="form" field="especie"></has-error>
                    </div>
                  </div>
                  <div class="form-group row">
                    <label for="Descripción" class="col-sm-12  col-md-4 col-form-label">Descripción</label>
                    <div class="col-sm-12 col-md-8">
                      <input type="text" class="form-control" id="descripcion"  :class="{ 'is-invalid': form.errors.has('descripcion') }" v-model="form.descripcion">
                       <has-error :form="form" field="descripcion" />
                    </div>
                  </div>
                  <div class="form-group row">
                    <div class="col-sm-12 text-right">
                      <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-primary"  v-text="editando ==0 ? 'Crear' : 'Actualizar'"></button>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
import { Form } from "vform";
    
    export default {
        data() {
            return {
                editando: 0,
                form: new Form({
                    id : '',
                    especie : '',
                    descripcion : '',
                }),
               
                listado: []
            }
        },
        methods: {
            guardar(){
                let me = this;
                this.form.post('api/especies')
                    .then(({data})=>{
                      editando: 0,
                      me.listar();
                      $('#modalEspecies').modal('hide');
                      me.form.especie = '';
                      me.form.descripcion = '';
                    })
            },
            abrirCrear(){
                this.editando = 0;
                this.form.reset(); 
                $('#modalEspecies').modal('show');
            },
         
            listar(){
              let me = this;
              axios.get("api/especies")
              .then(function (response) {
                  me.listado = response.data
              });
            },
            cargaEditar(objeto){
              let me = this;
              this.form.fill(objeto);
              this.editando = 1;
                $('#modalEspecies').modal('show');
            },
            editar(){
                let me = this;
                this.form.put('api/especies/'+this.form.id)
                    .then(({data})=>{
                    
                        $('#modalEspecies').modal('hide');
                        me.listar();
                    })
          
            },
            eliminar(index){
                let me = this;
                Swal.fire({
                  title: "Estás seguro?",
                  text: "Una vez eliminado, no se puede recuperar este registro",
                  icon: "warning",
                  showCancelButton: true,
                  confirmButtonColor: '#c7120c',
                  cancelButtonText: 'Cancelar',
                  confirmButtonText: 'Aceptar!',
                  reverseButtons: true
                })
                .then((result) => {
                    if (result.isConfirmed) {
                        me.form.delete('api/especies/'+index)
                        .then(({data})=>{
                            me.listar();
                        })
                    }
                });
                 
            }
        },
        mounted() {
          this.listar();
        }
    }
</script>
