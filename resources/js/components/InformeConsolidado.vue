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
                            <div class="form-group col-3">
                                <label for="estado_siembta">Estado de siembra</label>
                                <select name="f_estado" class="custom-select" id="f_estado" v-model="f_estado">
                                    <option value="-1">Todas</option>
                                    <option value="1">Activas</option>
                                    <option value="0">Inactivas</option>
                                </select>
                            </div>
                            <div class="form-group col-3">
                                <label for="siembra_activa">Siembras
                                    {{ f_estado == 0 ? "inactivas" : "activas" }} :
                                </label>
                                <template v-if="f_estado != '-1'">
                                    <v-select :options="filteredItems" label="nombre_siembra"
                                        :reduce="(siembra) => siembra.id" v-model="f_siembra" />
                                </template>
                                <template v-if="f_estado == '-1'">
                                    <v-select :options="listadoSiembras" label="nombre_siembra"
                                        :reduce="(siembra) => siembra.id" v-model="f_siembra" />
                                </template>
                            </div>
                            <div class="form-group col-md-2">
                                <label for="contenedor">Estanque:</label>
                                <select class="custom-select" id="contenedor" v-model="f_contenedor">
                                    <option value="-1">Seleccionar</option>
                                    <option :value="cont.id" v-for="(cont,
                                        index) in listadoEstanques" :key="index">{{ cont.contenedor }}</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                                <label for="phase" class="col-form-label">Fase
                                </label>
                                <v-select label="phase" class="w-100" v-model="search_phase" :reduce="(option) => option.id"
                                    :filterable="false" :options="listPhases" @search="onSearchPhase">
                                    <template slot="no-options">
                                        Escribe para iniciar la búsqueda
                                    </template>
                                    <template slot="option" slot-scope="option">
                                        <div class="d-center">
                                            {{ option.phase }}
                                        </div>
                                    </template>
                                    <template slot="selected-option" slot-scope="option">
                                        <div class="selected d-center">
                                            {{ option.phase }}
                                        </div>
                                    </template>
                                </v-select>
                            </div>
                            <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                                <label for="search_type">Tipo de siembra</label>
                                <select name="search_type" class="custom-select w-100" id="search_type"
                                    v-model="search_type">
                                    <option value="">Todas</option>
                                    <option value="Monocultivo">Monocultivo</option>
                                    <option value="Policultivo">Policultivo</option>
                                </select>
                            </div>
                            <div class="form-group col-xs-6 col-sm-6 col-md-4 col-lg-3">
                                <label for="search_nro_results">Mostrar {{ search_nro_results }} por página</label>
                                <input type="number" v-model="search_nro_results" class="form-control">
                            </div>
                            <div class="form-group col-md-2">
                                <button class="btn btn-primary" @click="listar()">
                                    Filtrar resultados
                                </button>
                            </div>
                            <div class="form-group col-md-2">
                                <downloadexcel class="btn btn-success form-control" :fetch="fetchData" :fields="json_fields"
                                    name="informe-consolidado.xls" type="xls">
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
                                        <th>Area (m²)</th>
                                        <th>Tipo</th>
                                        <th>Fase</th>
                                        <th>Inicio siembra</th>
                                        <th>Tiempo de cultivo <br> <small>(Días)</small></th>
                                        <th>Tiempo de cultivo <br> <small>(Meses)</small></th>
                                        <th>Cant Inicial (animales)</th>
                                        <th>Biomasa Inicial (kg)</th>
                                        <th>Peso Inicial (g)</th>
                                        <th>Carga inicial (kg/m²) </th>
                                        <th>Cant final (animales)</th>
                                        <th>Peso Actual (g)</th>
                                        <th>Biomasa disponible (kg)</th>
                                        <th>Biomasa Cosechada (kg) </th>
                                        <th>Mortalidad (Animales)</th>
                                        <th>Mortalidad (Kg)</th>
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
                                        <th>Ganancia peso / día <br> (gr) </th>
                                        <th><b>%</b> Supervivencia final</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(le,
                                        index) in listadoExistencias.data" :key="index">
                                        <td v-text="index + 1"></td>
                                        <td v-text="le.nombre_siembra" class="fixed-column"></td>
                                        <td v-text="le.capacidad"></td>
                                        <td>{{ le.tipo }}</td>
                                        <td> 
                                            <span v-if="le.phase_id">
                                                {{ le.phase.phase }}
                                            </span> 
                                        </td>
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
                            <pagination :align="'center'" :data="listadoExistencias" :limit="2"
                                @pagination-change-page="listar">
                                <span slot="prev-nav"><i class="fas fa-arrow-left"></i></span>
                                <span slot="next-nav"><i class="fas fa-arrow-right"></i></span>
                            </pagination>
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
                "Tipo": "tipo",
                "Fase": "phase.phase",
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
                "Cantidad final \n (Animales)": {
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

                "Biomasa disponible \n (kg)": {
                    field: "biomasa_disponible",
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
                'Mortalidad (Animales)': {
                    field: "mortalidad",
                    callback: (value) => {
                        return numeral(value).format('0.00');
                    }
                },
                "Mortalidad (kg)": {
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
                "Ganancia peso/dia \n (gr)": {
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
            listadoExistencias: {},
            listadoEspecies: [],
            listadoSiembras: [],
            listPhases: [],
            imprimirRecursos: [],
            listadoEstanques: [],
            f_siembra: "-1",
            f_contenedor: "-1",
            f_estado: "1",
            f_inicio_d: "-1",
            f_inicio_h: "-1",
            search_phase: "",
            search_type: "",
            search_nro_results: "15"
        };
    },
    components: {
        downloadexcel
    },
    computed: {
        filteredItems() {
            return this.listadoSiembras.filter((item) => item.estado == this.f_estado);
        }
    },
    methods: {
        async fetchData() {
            return this.listadoExistencias.data;
        },
        listar(page = 1) {
            let me = this;

            const data = {
                'f_siembra': this.f_siembra == '-1' ? '-1' : this.f_siembra,
                'f_contenedor': this.f_contenedor == '-1' ? '-1' : this.f_contenedor,
                'f_estado': this.f_estado == '-1' ? '-1' : this.f_estado,
                'f_inicio_d': this.f_inicio_d == '-1' ? '-1' : this.f_inicio_d,
                'f_inicio_h': this.f_inicio_h == '-1' ? '-1' : this.f_inicio_h,
                phase: this.search_phase,
                type: this.search_type,
                nro_results: this.search_nro_results,
                page: page
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
        listarSiembras() {
            let me = this;
            axios.get("api/siembras/listado").then(function (response) {
                me.listadoSiembras = response.data;
            });
        },
        listarEstanques() {
            let me = this;
            axios.get("api/contenedores").then(function (response) {
                me.listadoEstanques = response.data;
            });
        },
        onSearchPhase(search, loading) {
            if (search.length) {
                loading(true);
                let data = {
                    phase: search
                };

                axios.get(`api/phases/get`, {
                    params: data
                })
                    .then((response) => {
                        this.listPhases = (response.data.phases.data);
                        loading(false)
                    })
                    .catch(e => console.log(e))
            }
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
