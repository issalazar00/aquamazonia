<template>
	<!-- Modal -->
	<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" data-backdrop="static"
		aria-labelledby="Agregar/Editar" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLongTitle">Datos Usuario</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form method="POST" action="/vuejs/form" @submit.prevent="guardar">

					<div class="modal-body">

						<div :class="['form-group row', form.errors().has('name') ? 'has-error' : '']">
							<label for="name" class="col-sm-12 col-md-4 col-form-label">Nombre</label>
							<div class="col-sm-12 col-md-8">
								<input id="name" name="name" value="" autofocus="autofocus" class="form-control" type="text"
									v-model="form.name" required>
								<span v-if="form.errors().has('name')" class="label label-danger"
									v-text="form.errors().get('name')"></span>
							</div>
						</div>
						<div :class="['form-group row', form.errors().has('email') ? 'has-error' : '']">
							<label for="email" class="col-sm-12 col-md-4 col-form-label">Correo</label>
							<div class="col-sm-12 col-md-8">
								<input id="email" name="email" class="form-control" type="email" v-model="form.email">
								<span v-if="form.errors().has('email')" class="label label-danger"
									v-text="form.errors().get('email')"></span>
							</div>

						</div>
						<div :class="['form-group row', form.errors().has('password') ? 'has-error' : '']">
							<label for="password" class="col-sm-12 col-md-4 col-form-label">Password</label>
							<div class="col-sm-12 col-md-8">
								<input id="password" name="password" class="form-control" type="password" v-model="form.password">
								<span v-if="form.errors().has('password')" class="label label-danger"
									v-text="form.errors().get('password')"></span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="submit" class="btn btn-primary" v-text="editando == 0 ? 'Guardar' : 'Actualizar'"></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</template>
<script>
import form from 'vuejs-form'
export default {
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
		guardar() {
			let me = this;
			if (!this.form.errors().any()) {
				if (me.editando == 0) {
					if (me.form.password == '') {
						alert("Debe digitar la contraseña !!!");
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
			this.$emit('list-users')
		},

	},
}
</script>
