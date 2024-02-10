<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Gestión de alimentos</div>

          <div class="card-body">
            <div class="row mb-1">
              <div class="col-12 text-right">
                <button class="btn btn-success" @click="$refs.FormFood.ResetData()" data-toggle="modal"
                  data-target="#modalFoods">
                  Añadir Alimento
                </button>
              </div>
            </div>
            <div class="row">
              <table class="table table-hover table-sm table-bordered">
                <thead class="thead-primary">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Alimento</th>
                    <th scope="col">Costo Kl</th>
                    <th>Bodega</th>
                    <th>Marca</th>
                    <th>Categoría</th>
                    <th>Proveedor</th>
                    <th scope="col">Opciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(alimento, index) in listado" :key="index">
                    <th scope="row" v-text="index + 1"></th>
                    <td v-text="alimento.alimento"></td>
                    <td v-text="alimento.costo_kg"></td>
                    <td>{{ alimento.warehouse ? alimento.warehouse.warehouse : "" }}</td>
                    <td>{{ alimento.brand ? alimento.brand.brand : "" }}</td>
                    <td>{{ alimento.category ? alimento.category.category : "" }}</td>
                    <td>{{ alimento.provider ? alimento.provider.provider : "" }}</td>
                    
                    <td>
                      <button @click="ShowData(alimento)" class="btn btn-success" type="button">
                        <i class="fas fa-edit"></i>
                      </button>
                      <button @click="Delete(alimento.id)" class="btn btn-danger" type="button">
                        <i class="fas fa-trash"></i>
                      </button>
                      <button class="btn btn-primary" type="button" @click="ShowCosts(alimento.id)">
                        <i class="fas fa-dollar-sign"></i>
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
    <form-food ref="FormFood" @list-foods="listFoods"></form-food>
    <food-history ref="FoodHistory"></food-history>
  </div>
</template>

<script>
import FormFood from "./form-food.vue";
import FoodHistory from "./food-history.vue";

export default {
  data() {
    return {
      listado: [],
    };
  },
  components: {
    FormFood,
    FoodHistory
  },
  methods: {
    listFoods() {
      let me = this;
      axios.get("api/alimentos").then(function (response) {
        me.listado = response.data;
      });
    },
    ShowData(food) {
      this.$refs.FormFood.OpenEditFood(food);
    },

    Delete(foodId) {
      let me = this;
      Swal.fire({
        title: "Estás seguro?",
        text: "Una vez eliminado, no se puede recuperar este registro",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#c7120c",
        cancelButtonText: "Cancelar",
        confirmButtonText: "Aceptar!",
        reverseButtons: true,
        dangerMode: true,
      }).then((result) => {
        if (result.isConfirmed) {
          me.form.delete("api/alimentos/" + foodId).then(({ data }) => {
            me.listFoods();
          });
        }
      });
    },

    ShowCosts(idAlimento) {
      this.$refs.FoodHistory.ShowCosts(idAlimento);
    },
    closeModal: function () {
      let me = this;
      this.$refs.FormFood.ResetData();
      me.listFoods(1);
    },

  },
  mounted() {
    this.listFoods();
  },
};
</script>
