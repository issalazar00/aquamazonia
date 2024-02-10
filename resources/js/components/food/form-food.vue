<template>
  <div class="modal fade" id="modalFoods" tabindex="-1" data-backdrop="static" role="dialog"
    aria-labelledby="modalFoodsLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalalimentosLabel" v-text="edit == 0 ? 'Crear alimento' : 'Actualizar alimento'">
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form @submit.prevent="SaveFood()">
            <div class="form-group row">
              <label for="alimento" class="col-sm-12 col-md-4 col-form-label">Nombre Alimento</label>
              <div class="col-sm-12 col-md-8">
                <input type="text" class="form-control" id="alimento" :class="{ 'is-invalid': form.errors }"
                  v-model="form.alimento" />
              </div>
            </div>
            <div class="form-group row">
              <label for="costo" class="col-sm-12 col-md-4 col-form-label">Costo KL
              </label>
              <div class="col-sm-12 col-md-8">
                <input type="text" class="form-control" id="costo_kg" v-model="form.costo_kg"
                  :class="{ 'is-invalid': form.errors }" />
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
                :reduce="(option) => option.id" :filterable="false" :options="optWarehouses"
                @search="onSearchWarehouse">
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
        alimento: "",
        costo_kg: "",
        warehouse_id: "",
        category_id: "",
        provider_id: "",
        brand_id: ""
      }),
    }
  },
  methods: {
    CreateFood() {
      let me = this;
      axios
        .post("api/alimentos", me.form)
        .then(({ data }) => {
          $("#modalFoods").modal("hide");
          me.form = {};
          me.$emit('list-foods')
        })
        .catch((response) => {
          console.log(response.response)
        });
    },
    OpenEditFood(food) {
      let me = this;
      me.edit = true;
      $("#modalFoods").modal("show");
      me.form = food;
      console.log(me.form)
    },

    SaveFood: function () {
      if (this.edit == false) {
        this.CreateFood();
      } else {
        this.EditFood();
      }
    },
    EditFood() {
      let me = this;
      axios.put(`api/alimentos/${this.form.id}`, this.form)
        .then(data => {
          $("#modalFoods").modal("hide");
          me.form = {}
          me.$emit('list-foods')
        })
        .catch((response) => {
          console.log(response.response)
        });
    },

    ResetData() {
      let me = this;
      $("#modalFoods").modal("hide");
      me.form = {};
      this.assignErrors(false);
    },

    closeModal() {
      this.edit = false;
      this.ResetData();
      this.$emit("list-foods");
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
        axios.get(`api/food-categories?category=${search}&page=1`)
          .then((response) => {
            this.optCategories = (response.data.foodCategories.data);
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