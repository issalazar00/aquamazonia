<template>
	<div class="container">
		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="card">
					<div class="card-header">Gestión de Usuarios</div>

					<div class="card-body">
						<div class="row mb-1">
							<div class="col-12 ">
								<button class="btn btn-success float-right" @click="limpiar()" type="button" data-toggle="modal"
									data-target="#modalUser">
									Agregar
								</button>
							</div>
						</div>
						<div class="row">
							<table class="table table-striped table-sm">
								<thead>
									<tr>
										<th scope="col">#</th>
										<th scope="col">Nombre</th>
										<th scope="col">Correo</th>
										<th scope="col">Estado</th>
										<th scope="col">Opciones</th>
									</tr>
								</thead>
								<tbody>
									<tr v-for="usuario in listado" :key="usuario.id">
										<td></td>
										<td v-text="usuario.name"></td>
										<td v-text="usuario.email"></td>
										<td v-text="usuario.estado == 1 ? 'Activo' : 'Inacitvo'"></td>
										<td>
											<!-- <span style="font-size: 1.5em; color:#FFC107;"><i class="fas fa-user"></i></span>-->
											<button @click="editar(usuario)" class="btn btn-success" type="button" data-toggle="modal"
												data-target="#modalUser">
												<i class="fas fa-edit"></i>
											</button>
											<button class="btn btn-danger" @click="eliminar(usuario.id)">
												<i class="fas fa-trash"></i>
											</button>
										</td>
									</tr>
									<tr></tr>
									<tr></tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<form-users @list-users="listar()" />
	</div>
</template>

<script>
import form from 'vuejs-form'
import formUsers from './form-users.vue';
export default {
	components: { formUsers },
	data() {
		return {
			editando: 0,
			id_edita: 0,
			form: form.default({
				name: '',
				email: '',
				password: '',
				estado: 1,
			})
				.rules({
					email: 'email|min:7|required',
					name: 'required|min:5|',
				})
				.messages({
					'name.name': 'El nombre es requerido',
					'email.email': 'Ingrese un correo válido',

				}),
			errores: [],
			success: false,
			listado: []
		}
	},

	methods: {
		limpiar() {
			this.form.name = '';
			this.form.email = '';
			this.form.password = '';
			this.id_edita = '';
			this.editando = 0;
		},
		eliminar(id_elim) {
			let me = this;
			if (confirm('Esta seguro de inactivar este usuario?')) {
				axios.delete("api/usuarios/" + id_elim)
					.then(function (response) {
						me.listar();
					})
					.catch(function (error) {
					});
			}
		},
		guardar() {
			let me = this;
			if (!this.form.errors().any()) {
				if (me.editando == 0) {
					if (me.form.password == '') {
						alert("Debe digitar el password!!!");
					} else {
						axios.post("api/usuarios", this.form.all())
							.then(function (response) {

								me.form.email = '';
								me.form.name = '';
								me.form.password = '';
								$('#modalUser').modal('hide');
								me.listar();
							})
							.catch(function (error) {
							});
					}
				} else {
					axios.put("api/usuarios/" + this.id_edita, this.form.all())
						.then(function (response) {
							me.form.email = '';
							me.form.name = '';
							me.form.password = '';
							$('#modalUser').modal('hide');
							me.listar();
						})
						.catch(function (error) {
						});

				}
			}
			else {
			}
		},
		listar() {
			let me = this;
			axios.get("api/usuarios")
				.then(function (response) {
					me.listado = response.data;
				});

		},

		editar(objeto) {
			this.id_edita = objeto.id;
			this.editando = 1;
			this.form.name = objeto.name;
			this.form.email = objeto.email;
			this.editando = 1;
		}
	},
	mounted() {
		this.listar();
	}
}
</script>
