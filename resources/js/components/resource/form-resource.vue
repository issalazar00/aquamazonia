<template>
  <div class="modal fade" id="modalResources" tabindex="-1" data-backdrop="static" role="dialog"
    aria-labelledby="modalResourcesLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalrecursosLabel" v-text="edit == 0 ? 'Crear recurso' : 'Actualizar recurso'">
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="SaveResource()">
            <div class="form-group row">
              <label for="recurso" class="col-sm-12 col-md-4 col-form-label">Nombre recurso</label>
              <div class="col-sm-12 col-md-8">
                <input type="text" class="form-control" id="recurso" v-model="form.recurso" />
              </div>
            </div>
            <div class="form-group row">
              <label for="unidad" class="col-sm-12 col-md-4 col-form-label">Unidad
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="text" class="form-control" id="unidad" v-model="form.unidad"
                  placeholder="Kg, Kl, Minuto, Lt" />
              </div>
            </div>
            <div class="form-group row">
              <label for="costo" class="col-sm-12 col-md-4 col-form-label">Costo Unidad
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="text" class="form-control" id="costo" v-model="form.costo" />
              </div>
            </div>
            <div class="form-group row">
              <label for="expiration_date" class="col-sm-12 col-md-4 col-form-label">Fecha de vencimiento
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="date" class="form-control" id="expiration_date" v-model="form.expiration_date" />
              </div>
            </div>
            <div class="form-group row">
              <label for="cantidad" class="col-sm-12 col-md-4 col-form-label">Cantidad
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="number" class="form-control" id="cantidad" v-model="form.cantidad" />
              </div>
            </div>
            <div class="form-group row">
              <label for="cantidad_minima" class="col-sm-12 col-md-4 col-form-label">Cantidad mínima
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="number" class="form-control" id="cantidad_minima" v-model="form.cantidad_minima" />
              </div>
            </div>
            <div class="form-group row">
              <label for="cantidad_maxima" class="col-sm-12 col-md-4 col-form-label">Cantidad máxima
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="number" class="form-control" id="cantidad_maxima" v-model="form.cantidad_maxima" />
              </div>
            </div>

            <div class="form-group row">
              <label for="categoria" class="col-sm-12 col-md-4 col-form-label">Categoría
              </label>
              <v-select label="category" class="col-sm-12 col-md-8" v-model="form.category_id"
                :reduce="(option) => option.id" :filterable="false" :options="optCategories" @search="onSearchCategory">
                <template slot="no-options">
                  Escribe para iniciar la búsqueda
                </template>
                <template slot="option" slot-scope="option">
                  <div class="d-center">
                    {{ option.category }}
                  </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                  <div class="selected d-center">
                    {{ option.category }}
                  </div>
                </template>
              </v-select>
            </div>
            <div class="form-group row">
              <label for="warehouse" class="col-sm-12 col-md-4 col-form-label">Bodega
              </label>
              <v-select label="warehouse" id="warehouse" class="col-sm-12 col-md-8" v-model="form.warehouse_id"
                :reduce="(option) => option.id" :filterable="false" :options="optWarehouses" @search="onSearchWarehouse">
                <template slot="no-options">
                  Escribe para iniciar la búsqueda
                </template>
                <template slot="option" slot-scope="option">
                  <div class="d-center">
                    {{ option.warehouse }}
                  </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                  <div class="selected d-center">
                    {{ option.warehouse }}
                  </div>
                </template>
              </v-select>
            </div>
            <div class="form-group row">
              <label for="provider" class="col-sm-12 col-md-4 col-form-label">Proveedor
              </label>
              <v-select label="provider" id="provider" class="col-sm-12 col-md-8" v-model="form.provider_id"
                :reduce="(option) => option.id" :filterable="false" :options="optProviders" @search="onSearchProvider">
                <template slot="no-options">
                  Escribe para iniciar la búsqueda
                </template>
                <template slot="option" slot-scope="option">
                  <div class="d-center">
                    {{ option.provider }}
                  </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                  <div class="selected d-center">
                    {{ option.provider }}
                  </div>
                </template>
              </v-select>
            </div>
            <div class="form-group row">
              <label for="brand" class="col-sm-12 col-md-4 col-form-label">Marca
              </label>
              <v-select label="brand" id="brand" class="col-sm-12 col-md-8" v-model="form.brand_id"
                :reduce="(option) => option.id" :filterable="false" :options="optBrands" @search="onSearchBrand">
                <template slot="no-options">
                  Escribe para iniciar la búsqueda
                </template>
                <template slot="option" slot-scope="option">
                  <div class="d-center">
                    {{ option.brand }}
                  </div>
                </template>
                <template slot="selected-option" slot-scope="option">
                  <div class="selected d-center">
                    {{ option.brand }}
                  </div>
                </template>
              </v-select>
            </div>
            <div class="form-group row">
              <div class="col-sm-12 text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                  Cancelar
                </button>
                <button type="submit" class="btn btn-primary" v-text="edit == 0 ? 'Crear' : 'Actualizar'"></button>
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
</template>

<script>

import { Form } from "vform";

export default {
  data() {
    return {
      optWarehouses: [],
      optBrands: [],
      optProviders: [],
      optCategories: [],
      edit: 0,
      form: new Form({
        id: "",
        recurso: "",
        unidad: "",
        costo: "",
        warehouse_id: "",
        category_id: "",
        provider_id: "",
        brand_id: "",
        expiration_date: "",
        cantidad: "",
        cantidad_maxima: "",
        cantidad_minima: ""
      }),
    }
  },
  methods: {
    CreateResource() {
      let me = this;
      axios
        .post("api/recursos", me.form)
        .then(({ data }) => {
          $("#modalResources").modal("hide");
          me.form = {};
          me.$emit('list-resources')
        })
        .catch((response) => {
          console.log(response.response)
        });
    },
    OpenEditResource(resource) {
      let me = this;
      me.edit = true;
      $("#modalResources").modal("show");
      me.form = resource;
      console.log(me.form)
    },

    SaveResource: function () {
      if (this.edit == false) {
        this.CreateResource();
      } else {
        this.EditResource();
      }
    },
    EditResource() {
      let me = this;
      axios.put(`api/recursos/${this.form.id}`, this.form)
        .then(data => {
          $("#modalResources").modal("hide");
          me.form = {}
          me.$emit('list-resources')
        })
        .catch((response) => {
          console.log(response.response)
        });
    },

    ResetData() {
      let me = this;
      $("#modalResources").modal("hide");
      me.form = {};
      this.assignErrors(false);
    },

    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-resources");
    },

    assignErrors(response) {
      if (response) {
        var errors = response.response.data.errors;
      } else { }
    },

    onSearchWarehouse(search, loading) {
      if (search.length) {
        loading(true);
        axios.get(`api/warehouses?warehouse=${search}&page=1`)
          .then((response) => {
            this.optWarehouses = (response.data.warehouses.data);
            loading(false)
          })
          .catch(e => console.log(e))
      }
    },
    onSearchBrand(search, loading) {
      if (search.length) {
        loading(true);
        axios.get(`api/brands?brand=${search}&page=1`)
          .then((response) => {
            this.optBrands = (response.data.brands.data);
            loading(false)
          })
          .catch(e => console.log(e))
      }
    },
    onSearchCategory(search, loading) {
      if (search.length) {
        loading(true);
        axios.get(`api/resource-categories?category=${search}&page=1`)
          .then((response) => {
            this.optCategories = (response.data.resourceCategories.data);
            loading(false)
          })
          .catch(e => console.log(e))
      }
    },
    onSearchProvider(search, loading) {
      if (search.length) {
        loading(true);
        axios.get(`api/providers?provider=${search}&page=1`)
          .then((response) => {
            this.optProviders = (response.data.providers.data);
            loading(false)
          })
          .catch(e => console.log(e))
      }
    },
  }
}


</script>