<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        Informe consolidado variables de producción
                    </div>
                    <!-- <a href="informe-excel"><button type="submit" class="btn btn-success" name="infoSiembras"><i class="fa fa-fw fa-download"></i> Generar Excel </button></a> -->
                    <div class="card-body">
                        <div class="row text-left">
                            <h5>Filtrar por:</h5>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-2">
                                <label for="f_estado">
                                    Estado:
                                    <select class="custom-select" name="estado" id="estado" v-model="f_estado">
                                        <option value="-1">--Seleccionar--</option>
                                        <option value="0">Inactiva</option>
                                        <option value="1">Activa</option>
                                    </select>
                                </label>
                            </div>
                            <div class="form-group col-3" v-show="f_estado == 1">
                                <label for="siembra_activa">Siembras Activas</label>
                                <select name="siembra_activa" class="custom-select" id="siembra_activa"
                                    v-model="f_siembra">
                                    <option v-for="(siembraActiva,
                                    index) in siembrasActivas" :key="index" :value="siembraActiva.id">{{
            siembraActiva.nombre_siembra
    }}</option>
                                </select>
                            </div>
                            <div class="form-group col-3" v-show="f_estado == 0">
                                <label for="siembra_inactiva">Siembras Inactivas</label>
                                <select name="siembra_inactiva" class="custom-select" id="siembra_inactiva"
                                    v-model="f_siembra">
                                    <option v-for="(siembraInactiva,
                                    index) in siembrasInactivas" :key="index" :value="siembraInactiva.id">{{
            siembraInactiva.nombre_siembra
    }}</option>
                                </select>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="contenedor">Estanque:</label>
                                <select class="custom-select" id="contenedor" v-model="f_contenedor">
                                    <option value="-1">Seleccionar</option>
                                    <option :value="cont.id" v-for="(cont,
                                    index) in listadoEstanques" :key="index">{{ cont.contenedor }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <button class="btn btn-primary" @click="listar()">
                                    Filtrar resultados
                                </button>
                            </div>
                            <div class="form-group col-md-2">
                                <downloadexcel class="btn btn-success form-control" :fetch="fetchData"
                                    :fields="json_fields" name="informe-consolidado.xls" type="xls">
                                    <i class="fa fa-fw fa-download"></i> Generar
                                    Excel
                                </downloadexcel>
                            </div>
                        </div>
                        <div class="table-container" id="table-container2">
                            <table class="table-sticky table table-sm table-hover table-bordered">
                                <thead class="thead-primary">
                                    <tr>
                                        <th>#</th>
                                        <th class="fixed-column">Siembra</th>
                                        <th>Area</th>
                                        <th>Inicio siembra</th>
                                        <th>Tiempo de cultivo <br> <small>(Días)</small></th>
                                        <th>Tiempo de cultivo <br> <small>(Meses)</small></th>
                                        <th>Cant Inicial</th>
                                        <th>Biomasa Inicial</th>
                                        <th>Peso Inicial</th>
                                        <th>Carga inicial</th>
                                        <th>Animales final</th>
                                        <th>Peso Actual</th>
                                        <th>Biomasa dispo</th>
                                        <th>Biomasa Cosechada</th>
                                        <th>Mortalidad Animales</th>
                                        <th>Mort. Kg</th>
                                        <th>% Mortalidad</th>
                                        <th>Animales cosechados</th>
                                        <th>
                                            Densidad Inicial
                                            (Animales/m<sup>2</sup>)
                                        </th>
                                        <th>
                                            Densidad Final
                                            (Animales/m<sup>2</sup>)
                                        </th>
                                        <th>Carga Final (Kg/m<sup>2</sup>)</th>
                                        <th>Horas Hombre</th>
                                        <th>Costo Horas</th>
                                        <th>Costo Recursos</th>
                                        <th>Costo Alimentos</th>
                                        <th>Total alimento (Kg)</th>
                                        <th>Costo Total</th>
                                        <th>Costo produccion final</th>
                                        <th>Conversion alimenticia parcial</th>
                                        <th>Conversion final</th>
                                        <th>Ganancia peso día</th>
                                        <th><b>%</b> Supervivencia final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(le,
                                    index) in listadoExistencias" :key="index">
                                        <td v-text="index + 1"></td>
                                        <td v-text="le.nombre_siembra" class="fixed-column"></td>
                                        <td v-text="le.capacidad"></td>
                                        <td v-text="le.fecha_inicio"></td>
                                        <td v-text="le.intervalo_tiempo"></td>
                                        <td>{{ le.intervalo_tiempo_months | numeral('0.00') }} meses</td>
                                        <td>
                                            {{ le.cantidad_inicial | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.biomasa_inicial | numeral('0.00') }}</td>
                                        <td>
                                            {{ le.peso_inicial | numeral('0.00') }} gr
                                        </td>
                                        <td>
                                            {{ le.carga_inicial | numeral('0.00') }}</td>
                                        <td>
                                            {{ le.cant_actual | numeral('0.00') }}</td>
                                        <td>
                                            {{ le.peso_actual | numeral('0.00') }} gr
                                        </td>
                                        <td>
                                            {{ le.biomasa_disponible | numeral('0.00') }} kg
                                        </td>
                                        <td>
                                            {{ le.salida_biomasa | numeral('0.00') }} kg
                                        </td>

                                        <td>{{ le.mortalidad | numeral('0.00') }}</td>
                                        <td>
                                            {{ le.mortalidad_kg | numeral('0.00') }} kg
                                        </td>
                                        <td>
                                            {{ le.mortalidad_porcentaje | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.salida_animales_sin_mortalidad | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.densidad_inicial | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.densidad_final | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.carga_final | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.horas_hombre | numeral('0.00') }} horas</td>
                                        <td>
                                            {{ le.costo_minutosh | numeral('$0,0.00') }}</td>
                                        <td>
                                            {{ le.costo_total_recurso | numeral('$0,0.00') }}</td>
                                        <td>
                                            {{ le.costo_total_alimento | numeral('$0,0.00') }}
                                        </td>
                                        <td>
                                            {{ le.cantidad_total_alimento }}</td>
                                        <td>
                                            {{ le.costo_tot | numeral('$0,0.00') }}</td>
                                        <td>
                                            {{ le.costo_produccion_final | numeral('$0,0.00') }}
                                        </td>
                                        <td>
                                            {{ le.conversion_alimenticia_parcial | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.conversion_final | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.ganancia_peso_dia | numeral('0.00') }}
                                        </td>
                                        <td>
                                            {{ le.porc_supervivencia_final | numeral('0.00') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import downloadexcel from "vue-json-excel";
export default {
    data() {
        return {
            json_fields: {
                Siembra: "nombre_siembra",
                'Area (m<sup>2</sup>)': "capacidad",
                "Inicio siembra": "fecha_inicio",
                "Tiempo de cultivo \n (Días)": {
                    field: "intervalo_tiempo",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Tiempo de cultivo \n (Meses)": {
                    field: "intervalo_tiempo_months",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Cantidad Inicial \n (Animales) ": {
                    field: "cantidad_inicial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Biomasa inicial \n (Kg) ": {
                    field: "biomasa_inicial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Peso inicial  \n (g)": {
                    field: "peso_inicial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Carga inicial  \n (Kg / m<sup>2</sup> ) ": {
                    field: "carga_inicial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Animales final": {
                    field: "cant_actual",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Peso actual \n (g)": {
                    field: "peso_actual",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },

                "Biomasa cosechada \n (Kg)": {
                    field: "salida_biomasa",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                'Mortalidad Animales': {
                    field: "mortalidad",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Mortalidad kg": {
                    field: "mortalidad_kg",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Mortalidad %": {
                    field: "mortalidad_porcentaje",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Animales cosechados": {
                    field: "salida_animales_sin_mortalidad",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Densidad inicial (Animales/m2)": {
                    field: "densidad_inicial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Densidad final (Animales/m2)": {
                    field: "densidad_final",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Carga final (Kg/m2)": {
                    field: "carga_final",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Horas hombre": {
                    field: "horas_hombre",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Costo Horas": {
                    field: "costo_minutosh",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Costo total recursos": {
                    field: "costo_total_recurso",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },

                "Costo total alimentos": {
                    field: "costo_total_alimento",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Total Kg Alimento": {
                    field: "cantidad_total_alimento",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Costo total Siembra": {
                    field: "costo_tot",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Costo producccion final": {
                    field: "costo_produccion_final",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Conversión alimenticia parcial": {
                    field: "conversion_alimenticia_parcial",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Conversion final": {
                    field: "conversion_final",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Ganancia peso dia": {
                    field: "ganancia_peso_dia",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "% Supervivencia final": {
                    field: "porc_supervivencia_final",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                }
            },
            listadoExistencias: [],
            listadoEspecies: [],
            siembrasActivas: [],
            siembrasInactivas: [],
            imprimirRecursos: [],
            listadoEstanques: [],
            f_siembra: "-1",
            f_contenedor: "-1",
            f_estado: "1",
            f_inicio_d: "-1",
            f_inicio_h: "-1"
        };
    },
    components: {
        downloadexcel
    },
    methods: {
        async fetchData() {
            return this.listadoExistencias;
        },
        listar() {
            let me = this;

            const data = {
                'f_siembra': this.f_siembra == '-1' ? '-1' : this.f_siembra,
                'f_contenedor': this.f_contenedor == '-1' ? '-1' : this.f_contenedor,
                'f_estado': this.f_estado == '-1' ? '-1' : this.f_estado,
                'f_inicio_d': this.f_inicio_d == '-1' ? '-1' : this.f_inicio_d,
                'f_inicio_h': this.f_inicio_h == '-1' ? '-1' : this.f_inicio_h,
            };

            axios.get("api/traer-existencias-detalle", { params: data }).then(function (response) {
                me.listadoExistencias = response.data.existencias;
            });
        },
        listarEspecies() {
            let me = this;
            axios.get("api/especies").then(function (response) {
                me.listadoEspecies = response.data;
            });
        },
        listarSiembras(estado_siembra) {
            let me = this;
            axios
                .get("api/siembras?estado_siembra=" + estado_siembra)
                .then(function (response) {
                    me.siembrasActivas = response.data.listado_siembras;
                    me.siembrasInactivas =
                        response.data.listado_siembras_inactivas;
                });
        },
        listarEstanques() {
            let me = this;
            axios.get("api/contenedores").then(function (response) {
                me.listadoEstanques = response.data;
            });
        },
    },
    mounted() {
        this.listarEspecies();
        this.listarSiembras();
        this.listarEstanques();
        this.listar();
    }
};
</script>
